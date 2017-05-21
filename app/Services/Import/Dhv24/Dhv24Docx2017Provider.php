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
    const INVESTIGATION_DIAGNOSE = 'diagnose';

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
        'Причина обращения / Motivo de visita :' => self::INVESTIGATION_SYMPTOMS,
        'Данные осмотра / Exploraci ó n fisica :' => self::INVESTIGATION_ADDITIONAL_SURVEY,
        'Дополнительные исследования/ Pruebas complementarias :' => self::INVESTIGATION_ADDITIONAL_INVESTIGATION,
        'Лечение и рекомендации / Tratamiento e recomendaciones :' => self::INVESTIGATION_DIAGNOSE,
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
    )
    {
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
            'Дата,  место, время визита Fecha, lugar de visita SPAIN',
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
        // Generate new models for import
        $this->accident = BlankModels::defaultAccident();
        $this->doctorAccident = BlankModels::defaultDoctorAccident();
        $this->doctorAccident->status = DoctorAccident::STATUS_CLOSED;

        $accidentType = AccidentType::firstOrCreate(['title' => 'Insurance Case']);
        $this->accident->accident_type_id = $accidentType->id;
        $accidentStatus = AccidentStatus::firstOrCreate(['title' => 'Done by doctor', 'caseable_type' => DoctorAccident::class]);
        $this->accident->accident_status_id = $accidentStatus->id;

        $this->doctorAccident->accident()->save($this->accident);
        // data is absent in the docx
        $this->accident->contacts = '';

        $data = $this->tableExtractorService->extract($this->domService->toArray($this->readerService->getDom()));
        $tables = $data[ExtractTableFromArrayService::TABLES];

        // assistant, patient, ref_num
        $firstTableContainer=$this->tableExtractorService->extract($tables[1][2]);
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
        $patient = $this->getPatient(current($caseInfoArray[self::PATIENT_NAME]));
        $this->accident->patient_id = $patient->id;

        $this->loadAccidentInvestigations($tables[1][3][0]);
        $this->loadDiagnostics($tables[1][4][0]);

        $this->accident->title = str_limit('[' . $this->accident->ref_num . ']' . $patient->name . ' (' . $this->getAssistant()->title . ')', 255);

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

    private function loadServices(array $servicesTable)
    {
        $services = [];
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

        $this->doctorAccident->visit_time = $date;
        $city = City::firstOrCreate(['title' => $place]);
        $this->doctorAccident->city_id = $city->id;
    }

    private function loadDiagnostics(array $data)
    {
        $diagnostico = $this->tableExtractorService->extract($data);
        foreach($diagnostico[ExtractTableFromArrayService::TABLES][0][1][0] as $row) {
            $diagnoseStr = Arr::multiArrayToString($row);
            $commaPos = mb_strrpos($diagnoseStr, ',');
            if ($commaPos) {
                $diagnosticCategory = DiagnosticCategory::firstOrCreate(['title' => trim(mb_substr($diagnoseStr, $commaPos+1))]);
                $diagnose = Diagnostic::firstOrCreate([
                    'title' => trim(mb_substr($diagnoseStr, 0, $commaPos)),
                    'diagnostic_category_id' => $diagnosticCategory->id,
                ]);

                $this->doctorAccident->diagnostics()->attach($diagnose);
            } else {
                Log::debug('Last comma not found, but needed ');
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
        foreach ($this->investigations as $investigation => $key) {
            foreach ($mergedInvestigations as $mergedInvestigation) {
                if (mb_strpos($mergedInvestigation, $investigation) !== false) {
                    $founded[] = $key;
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
                        case self::INVESTIGATION_DIAGNOSE:
                            $this->doctorAccident->diagnose = $withoutKey;
                            break;
                        default:
                            Log::error('Investigation key is not defined (should be added as a case)', ['key' => $key]);
                    }
                }
            }
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
    private function getPatient($patientStr = '')
    {
        if (!$this->patient) {
            list($name, $birthday) = explode(',', $patientStr);

            $this->patient = Patient::firstOrCreate([
                'name' => trim(title_case($name)),
                'birthday' => strtotime($birthday)
            ]);
        }
        return $this->patient;
    }
}
