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
use App\DiagnosticCategory;
use App\Doctor;
use App\DoctorAccident;
use App\DoctorService;
use App\DoctorSurvey;
use App\Helpers\Arr;
use App\Helpers\BlankModels;
use App\Patient;
use app\Services\DocxReader\DocxReaderInterface;
use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\DomDocumentService;
use App\Services\ExtractTableFromArrayService;
use App\Services\Import\DataProvider;
use Illuminate\Support\Facades\Log;

class Dhv24Docx2017Provider extends DataProvider
{

    const PATIENT_NAME = 'P aciente , fecha de nacimiento';
    const ASSISTANCE_REF_NUM = 'Assistance Ref. num.';
    const DHV_REF_NUM = 'Ref.num. Doctor Home Visit';

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
        'Повторное обращение / Segunda visita:' => self::INVESTIGATION_SYMPTOMS,
        'Причина обращения / Motivo de visita :' => self::INVESTIGATION_SYMPTOMS,
        'Данные осмотра / Exploraci ó n fisica :' => self::INVESTIGATION_ADDITIONAL_SURVEY,
        'Дополнительные исследования/ Pruebas complementarias :' => self::INVESTIGATION_ADDITIONAL_INVESTIGATION,
        'Лечение и рекомендации / Tratamiento e recomendaciones :' => self::INVESTIGATION_RECOMMENDATION,
    ];

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
            'Наименование услуги, Сoncept',
            'TOTAL IMPORT, EUR',
            'Дата,  место, время визита',
            'Fecha, lugar de visita SPAIN',
            'A cargo de compañia',
            'Paciente , fecha de nacimiento',
            'Ref.num. Doctor Home Visit',
            'Assistance Ref.num.',
            'Наименование услуги, Сoncept',
            'Import, €',
            'Дата,  место, время визита',
            'Fecha, lugar de visita',
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
        $tables = $this->loadTables();

        // Generate new models for import
        $this->setUpDoctorAccidentDefaults();
        $this->loadAssistant($tables[1][2][1]);
        $this->PatientReferralNum($tables[1][2][3]);

        ///////

        $this->loadAccidentInvestigations($tables[1][3][0]);
        $this->loadDiagnostics($tables[1][4][0]);
        $this->loadDoctor($tables);
        $this->loadTitle();
        $this->loadServices($tables[2]);
        $this->loadVisitDateAndPlace($tables[4][0][1]);

        $this->accident->save();
        $this->doctorAccident->save();

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
        $tables = $data[ExtractTableFromArrayService::TABLES];

        $this->validate($tables);

        return $tables;
    }

    public function validate(array $tables)
    {
        $this->checkValidTables($tables);
        $this->checkCargoDeCompania($tables[1][2][0]);
        $this->checkPacienteDeNacimiento($tables[1][2][2]);
    }

    /**
     * checking that count of the table and structure are expected
     */
    private function checkValidTables(array $tables)
    {

    }

    /**
     * Initialize Accident and DoctorAccident models
     * also creates AccidentType and AccidentStatus models it haven't been created yer
     */
    private function setUpDoctorAccidentDefaults()
    {
        $this->accident = BlankModels::defaultAccident();
        $this->doctorAccident = BlankModels::defaultDoctorAccident();
        $this->doctorAccident->status = DoctorAccident::STATUS_CLOSED;

        $accidentType = AccidentType::firstOrCreate(['title' => 'Insurance Case']);
        $this->accident->accident_type_id = $accidentType->id;
        $accidentStatus = AccidentStatus::firstOrCreate([
            'title' => 'Done by doctor',
            'caseable_type' => DoctorAccident::class
        ]);
        $this->accident->accident_status_id = $accidentStatus->id;

        $this->doctorAccident->accident()->save($this->accident);
        // data is absent in the docx
        $this->accident->contacts = '';
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

        $this->accident->ref_num = str_replace(' ', '', current($caseInfoArray[self::DHV_REF_NUM]));
        $this->accident->assistant_ref_num = str_replace(' ', '', current($caseInfoArray[self::ASSISTANCE_REF_NUM]));

        $this->loadPatient(current($caseInfoArray[self::PATIENT_NAME]));
    }

    private function loadTitle()
    {
        $this->accident->title = str_limit('[' . $this->accident->ref_num . '] ' . $this->patient->name . ' (' . $this->getAssistant()->title . ')', 255);
    }

    private function loadDoctor(array $tables)
    {
        $checkPoint = Arr::multiArrayToString($tables[1][5][0]);
        if ($checkPoint == 'Н аименование услуги , С on cept') {
            // without Doctor
        } elseif (isset($tables[1][6][0]) && Arr::multiArrayToString($tables[1][6][0]) == 'Н аименование услуги , С on cept') {
            // with Doctor
            $doctorStr = Arr::multiArrayToString($tables[1][5][0]);
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
                'medical_board_num' => $medical_board_num
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
            $mService = DoctorService::firstOrNew([
                'title' => $service[0],
                'price' => $service[1],
            ]);

            $this->doctorAccident->serviceable()->attach($mService);
        }
    }

    private function loadVisitDateAndPlace($data)
    {
        $dateAndPlace = Arr::multiArrayToString($data);

        $comma = mb_strpos($dateAndPlace, ',');
        $date = str_replace(' ', '', mb_substr($dateAndPlace, 0, $comma));
        $place = trim(mb_substr($dateAndPlace, $comma+1));
        $this->accident->address = $place;

        $this->doctorAccident->visit_time = strtotime($date);
        $city = City::firstOrCreate(['title' => $place]);
        $this->doctorAccident->city_id = $city->id;
    }

    private function loadDiagnostics(array $data)
    {
        $diagnostico = $this->tableExtractorService->extract($data);
        foreach ( $diagnostico[ExtractTableFromArrayService::TABLES][0][1][0] as $row) {

            $diagnoseStr = Arr::multiArrayToString($row);
            $commaPos = mb_strrpos($diagnoseStr, ',');

            if ($commaPos) {

                $diagnose = Diagnostic::firstOrCreate([
                    'title' => trim(mb_substr($diagnoseStr, 0, $commaPos)),
                    'disease_code' => str_replace(' ', '', mb_substr($diagnoseStr, $commaPos+1)),
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

            $detected = false;
            foreach ($this->investigations as $investigation => $key) {
                if (mb_strpos($mergedInvestigation, $investigation) !== false) {
                    $detected = true;

                    $founded[] = $key;
                    $this->addInvestigation($key, $mergedInvestigation, $investigation);
                }
            }

            if (!$detected) {
                // everything that goes after the recommendation add there
                if (!empty($this->doctorAccident->recommendation)) {
                    $this->doctorAccident->recommendation .= "\n" . $mergedInvestigation;
                } else {
                    Log::error('String is not investigation', ['source' => $mergedInvestigation]);
                }
            }

        }
    }

    private function addInvestigation($key = '', $mergedInvestigation = '', $investigation = '')
    {
        $withoutKey = trim(str_replace($investigation, '', $mergedInvestigation));
        switch ($key) {
            case self::INVESTIGATION_SYMPTOMS:
                $this->accident->symptoms = $withoutKey;
                break;
            case self::INVESTIGATION_ADDITIONAL_SURVEY:
                $this->doctorAccident->surveable()->create([
                    'title' => 'Imported',
                    'description' => $withoutKey
                ]);
                break;
            case self::INVESTIGATION_ADDITIONAL_INVESTIGATION:
                $this->doctorAccident->investigation = $withoutKey;;
                break;
            case self::INVESTIGATION_RECOMMENDATION:
                $this->doctorAccident->recommendation = $withoutKey;
                break;
            default:
                Log::error('Investigation key is not defined (should be added as a case)', ['key' => $key]);
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
            list($name, $birthday) = explode(',', $patientStr);

            $this->patient = Patient::firstOrCreate([
                'name' => trim(title_case($name)),
                'birthday' => strtotime($birthday)
            ]);
        }

        $this->accident->patient_id = $this->patient->id;
        return $this->patient;
    }
}
