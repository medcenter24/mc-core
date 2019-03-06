<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Import\Dhv24;


use App\Accident;
use App\AccidentStatus;
use App\AccidentType;
use App\Assistant;
use App\City;
use App\Diagnostic;
use App\Doctor;
use App\DoctorAccident;
use App\DoctorService;
use App\Document;
use App\Helpers\Arr;
use App\Helpers\BlankModels;
use App\Patient;
use App\Services\AccidentStatusesService;
use App\Services\AccidentTypeService;
use App\Services\DocumentService;
use app\Services\DocxReader\DocxReaderInterface;
use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\DomDocumentService;
use App\Services\ExtractTableFromArrayService;
use App\Services\Import\DataProvider;
use App\Services\Import\ImporterException;
use Illuminate\Support\Facades\Log;

class Dhv24Docx2017Provider extends DataProvider
{
    const PATIENT_NAME = 'P aciente , fecha de nacimiento';
    const ASSISTANCE_REF_NUM = 'Assistance Ref. num.';
    const DHV_REF_NUM = 'Ref.num. Doctor Home Visit';
    const MARKER_REAPPOINTMENT = 'Повторное обращение / Segunda visita:';

    const INVESTIGATION_SYMPTOMS = 'symptoms';
    const INVESTIGATION_ADDITIONAL_SURVEY = 'additional_survey';
    const INVESTIGATION_ADDITIONAL_INVESTIGATION = 'additional_investigation';
    const INVESTIGATION_RECOMMENDATION = 'recommendation';

    /**
     * @var SimpleDocxReaderService
     */
    private $readerService;

    /**
     * @var ExtractTableFromArrayService
     */
    private $tableExtractorService;

    /**
     * @var DomDocumentService
     */
    private $domService;

    /**
     * Data about case
     * @var array
     */
    private $investigations = [
        'Повторное обращение / S egunda visita :' => self::INVESTIGATION_SYMPTOMS,
        'Причина обращения / Motivo de visita :' => self::INVESTIGATION_SYMPTOMS,
        'Данные осмотра / Exploraci ó n fisica :' => self::INVESTIGATION_ADDITIONAL_SURVEY,
        'Дополнительные исследования/ Pruebas complementarias :' => self::INVESTIGATION_ADDITIONAL_INVESTIGATION,
        'Лечение и рекомендации / Tratamiento e recomendaciones :' => self::INVESTIGATION_RECOMMENDATION,
    ];

    /** Tools */

    /**
     * Main tables
     * @var array
     */
    private $rootTables = [];

    /** Generated models from the imported file */

    /**
     * main accident
     * @var Accident
     */
    private $accident;

    /**
     * @var DoctorAccident
     */
    private $doctorAccident;

    /**
     * @var Assistant
     */
    private $assistant;

    /**
     * @var Patient
     */
    private $patient;

    /**
     * Dhv24Docx2017Provider constructor.
     * @param DocxReaderInterface|null $readerService
     * @param null $tableExtractorService
     * @param null $domService
     */
    public function __construct(
        DocxReaderInterface $readerService = null,
        $tableExtractorService = null,
        $domService = null
    ) {
        if ($readerService) {
            $this->readerService = $readerService;
        } else {
            $this->readerService = new SimpleDocxReaderService();
        }

        if ($tableExtractorService) {
            $this->tableExtractorService = $tableExtractorService;
        } else {
            $this->tableExtractorService = new ExtractTableFromArrayService([
                ExtractTableFromArrayService::CONFIG_TABLE => ['w:tbl'],
                ExtractTableFromArrayService::CONFIG_ROW => ['w:tr'],
                ExtractTableFromArrayService::CONFIG_CEIL => ['w:tc'],
            ]);
        }

        if ($domService) {
            $this->domService = $domService;
        } else {
            $this->domService = new DomDocumentService([
                DomDocumentService::STRIP_STRING => true,
                DomDocumentService::CONFIG_WITHOUT_ATTRIBUTES => true,
            ]);
        }
    }

    public function load($path = '')
    {
        $this->readerService->load($path);

        return $this;
    }

    public function check()
    {
        // point 1 main phrazes and their order
        $points = [
            'MEDICAL REPORT, INVOICE',
            'D  I  A  G  N  O  S  T  I  C  O',
            'TOTAL IMPORT, EUR',
            'Дата,  место, время визита',
            'Fecha, lugar de visita',
            'A cargo de compañia',
            'Paciente , fecha de nacimiento',
            'Ref.num. Doctor Home Visit',
            'Assistance Ref.num.',
            'Наименование услуги, Сoncept',
            'Import, €',
        ];

        $text = $this->readerService->getText();
        foreach ($points as $checkPoint) {
            if (mb_strpos($text, $checkPoint) === false) {
                Log::debug(__CLASS__ . ' File content is not matched', [
                    $this->readerService->getFilePath(),
                    $checkPoint,
                ]);

                return false;
            }
        }

        return true;
    }

    public function import()
    {
        $this->loadTables();

        // Generate new models for import
        $this->setUpDoctorAccidentDefaults();
        $this->loadAssistantPatientReferralNum($this->rootTables[1][2]);

        if ($this->isReappointment()) {
            $extracted = $this->tableExtractorService->extract($this->rootTables[1][3][0]);
            $investigations = $extracted[ExtractTableFromArrayService::CONTENT];
            $diagnostics = $extracted[ExtractTableFromArrayService::TABLES];
            $this->accident->parent_id = $this->getParentAccident()->id;
            $doctorData = $this->rootTables[1][4][0];
            // doctor should be in the 1.4.0
        } else {
            $investigations = $this->rootTables[1][3][0];
            $diagnostics = $this->tableExtractorService->extract($this->rootTables[1][4][0])[ExtractTableFromArrayService::TABLES];
            $doctorData = $this->rootTables[1][5][0];
        }
        $this->loadAccidentInvestigations($investigations);
        $this->loadDiagnostics($diagnostics);
        $this->loadDoctor($doctorData);

        $this->loadTitle();
        $this->loadServices($this->rootTables[2]);
        $this->loadVisitDateAndPlace($this->rootTables[4][0]);

        $this->accident->save();
        $this->doctorAccident->save();

        $this->loadDocuments();

        return true;
    }

    /**
     * Return last imported accident
     */
    public function getLastAccident()
    {
        return $this->accident;
    }

    /**
     * All documents should have main tables in the content;
     * @return mixed
     */
    private function loadTables()
    {
        $data = $this->tableExtractorService->extract($this->domService->toArray($this->readerService->getDom()));
        $this->rootTables = $data[ExtractTableFromArrayService::TABLES];
        $this->validateAccident();
    }

    /**
     * Validate check point positions
     */
    private function validateAccident()
    {
        $this->checkTables();
        $this->checkCargoDeCompaniaPacienteDeNacimiento();
        $this->checkReappointment();
        $this->checkReferralNumber();
    }

    /**
     * Checking that count of the table and structure are expected
     */
    private function checkTables()
    {
        $this->throwIfFalse(
            count($this->rootTables) == 5,
            'Count of the main tables is not equal 5 but dhv24 expect it'
        );
    }

    /**
     * Check that markers positions is matched
     */
    private function checkCargoDeCompaniaPacienteDeNacimiento()
    {
        $firstTableContainer = $this->tableExtractorService->extract($this->rootTables[1][2][0]);
        $firstTable = current($firstTableContainer[ExtractTableFromArrayService::TABLES]);
        $assistantInfo = current(array_shift($firstTable));
        $this->throwIfFalse(2 == count($assistantInfo), 'Assistant info includes 2 arrays');
        $assistantMarker = Arr::multiArrayToString(array_shift($assistantInfo));
        $this->throwIfFalse('A cargo de compañia' == $assistantMarker, 'Marker "Cargo de compania" not found');
    }

    /**
     * Check if this a second ... appointment then his parent exists
     */
    private function checkReappointment()
    {
        $this->throwIfFalse(
            !$this->isReappointment() || $this->getParentAccident(),
            'Child accident could not be loaded before parent accident'
        );
    }

    /**
     * Check that this referral number hasn't been used yet
     */
    private function checkReferralNumber()
    {
        $this->throwIfFalse(
            !Accident::where('ref_num', $this->getReferralNumber())->count(),
            'Referral number has already been used'
        );
    }

    /**
     * Looking for the parent accident
     * @return Accident
     */
    private function getParentAccident()
    {
        // check that parent already exists
        $ref = $this->getReferralNumber();

        if ($pos = mb_strrpos($ref, '_')) {
            $ref = mb_substr($ref, 0, $pos);
        } else {
            $this->throwIfFalse(
                false,
                'Child accident should has unique number in order in the ref_num but nothing provided, ref: ' . $ref
            );
        }

        return Accident::where('ref_num', $ref)->first();
    }

    private function isReappointment()
    {
        return mb_strpos($this->readerService->getText(), self::MARKER_REAPPOINTMENT) !== false;
    }

    private function throwIfFalse($condition, $message = '')
    {
        if ($condition === false) {
            throw new ImporterException($message);
        }
    }

    /**
     * Initialize Accident and DoctorAccident models
     * also creates AccidentType and AccidentStatus models it haven't been created yer
     */
    private function setUpDoctorAccidentDefaults()
    {
        $this->accident = BlankModels::accident();
        $this->accident->accident_status_id = AccidentStatus::where('title', AccidentStatusesService::STATUS_CLOSED)
            ->where('type', AccidentStatusesService::TYPE_ACCIDENT)->first();
        $this->doctorAccident = BlankModels::doctorAccident();

        // TODO Here should'nt be used direct model call I need to implement service providers!!!
        $accidentType = AccidentType::where('title', AccidentTypeService::ALLOWED_TYPES[0])->first();
        if (!$accidentType) {
            $accidentType = AccidentType::firstOrCreate(['title' => AccidentTypeService::ALLOWED_TYPES[0], 'description' => '']);
        }
        $this->accident->accident_type_id = $accidentType->id;
        $accidentStatus = AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_CLOSED,
            'type' => AccidentStatusesService::TYPE_ACCIDENT
        ]);
        $this->accident->accident_status_id = $accidentStatus->id;

        $this->doctorAccident->accident()->save($this->accident);
        // data is absent in the docx
        $this->accident->contacts = '';
    }

    private function getReferralNumber()
    {
        // assistant, patient, ref_num
        $firstTableContainer=$this->tableExtractorService->extract($this->rootTables[1][2]);
        $firstTable = current($firstTableContainer[ExtractTableFromArrayService::TABLES]);

        $caseInfoTable = array_map(function ($val1) {
            return array_map(function ($val2) {
                return Arr::multiArrayToString($val2);
            }, $val1);
        }, $firstTable);

        array_shift($caseInfoTable);
        // name, ref_num, dhv_ref_num
        $caseInfoArray = Arr::collectTableRows($caseInfoTable);

        return str_replace(' ', '', current($caseInfoArray[self::DHV_REF_NUM]));
    }

    /**
     * @param $table
     *
     * will be initialized Assistant, Patient, and Accident::$ref_num
     */
    private function loadAssistantPatientReferralNum($table)
    {
        // assistant, patient, ref_num
        $firstTableContainer=$this->tableExtractorService->extract($table);
        $firstTable = current($firstTableContainer[ExtractTableFromArrayService::TABLES]);
        $assistantInfo = current(array_shift($firstTable));

        // assistant information for case
        array_shift($assistantInfo);
        $assistant = Arr::multiArrayToString(array_shift($assistantInfo));

        $this->accident->assistant_id = $this->getAssistant($assistant)->id;

        $caseInfoTable = array_map(function ($val1) {
            return array_map(function ($val2) {
                return Arr::multiArrayToString($val2);
            }, $val1);
        }, $firstTable);

        // name, ref_num, dhv_ref_num
        $caseInfoArray = Arr::collectTableRows($caseInfoTable);

        $this->accident->ref_num = $this->getReferralNumber();
        $this->accident->assistant_ref_num = str_replace(' ', '', current($caseInfoArray[self::ASSISTANCE_REF_NUM]));
        if (mb_strpos($this->accident->assistant_ref_num, ',') !== false) {
            $this->accident->assistant_ref_num = explode(',', $this->accident->assistant_ref_num)[0];
        }
        $this->loadPatient(current($caseInfoArray[self::PATIENT_NAME]));
    }

    private function loadTitle()
    {
        $this->accident->title = str_limit('[' . $this->accident->ref_num . '] ' . $this->patient->name . ' (' . $this->getAssistant()->title . ')', 255);
    }

    private function loadDoctor($doctorData)
    {
        $doctorStr = Arr::multiArrayToString($doctorData);
        if ($doctorStr !== 'Н аименование услуги , С on cept') { // case without doctor
            // else with Doctor
            $dot = mb_strpos($doctorStr, '.');
            $num = mb_strpos($doctorStr, 'num.');
            $name = mb_substr($doctorStr, $dot, $num - $dot);
            $name = trim($name, '., ');
            $medical_board_num = mb_substr($doctorStr, $num);
            $medical_board_num = trim(str_replace('num.col.', '', $medical_board_num));
            $gender = str_replace(' ', '', mb_substr($doctorStr, 0, $dot));

            $doctor = Doctor::firstOrCreate([
                'name' => $name,
                'gender' => $gender == 'Dra' ? 'female' : ($gender == 'Dr' ? 'male' : 'none'),
                'medical_board_num' => $medical_board_num,
                'description' => '',
            ]);

            $this->doctorAccident->doctor_id = $doctor->id;
        }
    }

    /**
     * Load case services
     *
     * @param array $servicesTable
     */
    private function loadServices(array $servicesTable)
    {
        foreach ($servicesTable as $row) {
            $service = [];
            foreach ($row as $key => $col) {
                $service[$key] = Arr::multiArrayToString($col);
            }
            $price = $service[1];
            $price = trim(str_replace(' ', '', $price));
            $price = floatval($price);
            $mService = DoctorService::firstOrCreate([
                'title' => $service[0],
                'price' => $price,
                'description' => '',
            ]);

            $this->doctorAccident->services()->attach($mService);
        }
    }

    private function loadVisitDateAndPlace($data)
    {
        $time = '';
        $timeStr = str_replace(' ', '', Arr::multiArrayToString($data[0]));

        // time in the hole
        if (preg_match('/визита(\d\d:\d\d)Fecha/', $timeStr, $matches)) {
            $time = $matches[1];
        }

        $dateAndPlace = Arr::multiArrayToString($data[1]);

        $comma = mb_strpos($dateAndPlace, ',');
        $date = str_replace(' ', '', mb_substr($dateAndPlace, 0, $comma));
        $dateTime = $date . (empty($time) ? '' : ' ' . $time);
        $place = trim(mb_substr($dateAndPlace, $comma+1));
        $this->accident->address = $place;

        $this->doctorAccident->visit_time = date('Y-m-d H:i:s', strtotime($dateTime));
        $city = City::firstOrCreate(['title' => $place]);
        $this->accident->city_id = $city->id;
    }

    private function loadDiagnostics(array $data)
    {
        foreach ( $data[0][1][0] as $row) {

            $diagnoseStr = Arr::multiArrayToString($row);
            $commaPos = mb_strrpos($diagnoseStr, ',');

            if ($commaPos) {

                $diagnose = Diagnostic::firstOrCreate([
                    'title' => trim(mb_substr($diagnoseStr, 0, $commaPos)),
                    'disease_code' => str_replace(' ', '', mb_substr($diagnoseStr, $commaPos+1)),
                    'description' => '',
                ]);

                $this->doctorAccident->diagnostics()->attach($diagnose);
            } else {
                Log::debug('Diagnostic last comma not found, but needed for the disease code');
            }
        }
    }

    /**
     * reason, condition, addition, advices
     * @param array $data
     */
    private function loadAccidentInvestigations(array $data)
    {
        $mergedInvestigations = array_map(function ($row) {
            return Arr::multiArrayToString($row);
        }, $data);

        $founded = [];
        foreach ($mergedInvestigations as $mergedInvestigation) {

            foreach ($this->investigations as $investigation => $key) {
                if (mb_strpos($mergedInvestigation, $investigation) !== false) {
                    $founded[] = $key;
                }
            }
            if (!count($founded)) {
                Log::error('String is not investigation', ['source' => $mergedInvestigation]);
            } else {
                $key = last($founded);
                $this->addInvestigation($key, $mergedInvestigation, array_search($key, $this->investigations));
            }
        }
    }

    private function addInvestigation($key = '', $mergedInvestigation = '', $investigation = '')
    {
        $withoutKey = trim(str_replace($investigation, '', $mergedInvestigation));
        switch ($key) {
            case self::INVESTIGATION_SYMPTOMS:
                $this->accident->symptoms .= (!empty($this->accident->symptoms) ? "\n" : '') . $withoutKey;
                break;
            case self::INVESTIGATION_ADDITIONAL_SURVEY:
                $this->doctorAccident->surveys()->create([
                    'title' => 'Imported',
                    'description' => $withoutKey
                ]);
                break;
            case self::INVESTIGATION_ADDITIONAL_INVESTIGATION:
                $this->doctorAccident->investigation .= (!empty($this->doctorAccident->investigation) ? "\n" : '') . $withoutKey;;
                break;
            case self::INVESTIGATION_RECOMMENDATION:
                $this->doctorAccident->recommendation .= (!empty($this->doctorAccident->recommendation) ? "\n" : '') . $withoutKey;
                break;
            default:
                Log::error('Investigation key is not defined (should be added as a case)', ['key' => $key]);
                $this->throwIfFalse(false, 'Undefined investigation key: ' . $key);
        }
    }

    /**
     * With returning of the identifier will initialize $this->assistant
     * which could be used to update assistant
     *
     * @param string $assistantStr
     * @return Assistant
     */
    private function getAssistant($assistantStr = '')
    {
        if (!$this->assistant) {
            $title = $comment = '';
            // first caps lock it's a title all other it is
            if (preg_match('/^([A-Z ]+)(.*)$/', $assistantStr, $matches)) {
                $title = ucfirst(strtolower($matches[1]));
                $comment = trim($matches[2], '., ');
            }

            $this->assistant = Assistant::firstOrNew(['title' => $title]);

            if (empty($this->assistant->comment) && $comment){
                $this->assistant->comment = $comment;
                $this->assistant->save();
            }
        }
        return $this->assistant;
    }

    /**
     * @param string $patientStr
     * @return Patient
     */
    private function loadPatient($patientStr = '')
    {
        if (!$this->patient) {

            if (strpos($patientStr, ',') === false) {
                $data = ['name' => trim(title_case($patientStr))];
            } else {
                list($name, $birthday) = explode(',', $patientStr);
                $birthday = date('Y-m-d', strtotime($birthday));
                $data = [
                    'name' => trim(title_case($name)),
                    'birthday' => $birthday,
                ];
            }
            $this->patient = Patient::firstOrCreate($data);
        }

        $this->accident->patient_id = $this->patient->id;
        return $this->patient;
    }

    /**
     * Load documents into the case
     *
     * TODO exclude default images which exists on the all of the documents
     * todo for example: login and signature
     */
    private function loadDocuments()
    {
        $files = $this->readerService->getImages();
        foreach ($files as $file) {
            /** @var Document $document */
            $document = Document::create([
                'title' => $file['name']
            ]);

            $fileName = mt_rand(1234,4312).'_import.'.trim($file['ext']);
            if (\Storage::disk('imports')->exists($fileName)) {
                Log::error('The same file will be overwritten with the import process', ['name' => $fileName]);
            }

            $excludeDir = __DIR__ . DIRECTORY_SEPARATOR . 'exclude' . DIRECTORY_SEPARATOR;
            $excludedFiles = new \FilesystemIterator($excludeDir);
            /** @var \SplFileInfo $exclude */
            foreach ($excludedFiles as $exclude) {
                if (strcmp($file['imageContent'], file_get_contents($exclude->getRealPath())) === 0) {
                    continue 2;
                }
            }

            \Storage::disk('imports')->put($fileName, $file['imageContent']);
            $document
                ->addMedia(storage_path('imports'.DIRECTORY_SEPARATOR.$fileName))
                ->toMediaCollection(DocumentService::CASES_FOLDERS, DocumentService::DISC_IMPORTS);
            \Storage::disk('imports')->delete($fileName);

            $this->accident->documents()->attach($document);
            $this->accident->patient->documents()->attach($document);
        }
    }
}
