<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Import\Dhv24;


use App\Accident;
use App\Assistant;
use App\DoctorAccident;
use App\Helpers\Arr;
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

    /**
     * @return Accident
     */
    public function import()
    {
        $accident = new Accident();
        $doctorAccident = new DoctorAccident();

        $data = $this->tableExtractorService->extract($this->domService->toArray($this->readerService->getDom()));
        $tables = $data[ExtractTableFromArrayService::TABLES];

        // assistant, patient, ref_num
        $firstTableContainer=$this->tableExtractorService->extract($tables[1][2]);
        $firstTable = current($firstTableContainer[ExtractTableFromArrayService::TABLES]);
        $assistantInfo = current(array_shift($firstTable));

        // assistant information for case
        array_shift($assistantInfo);
        $assistant = Arr::multiArrayToString(array_shift($assistantInfo));

        $accident->assistant_id = $this->getAssistant($assistant);

        $caseInfoTable = array_map(function ($val1) {
            return array_map(function ($val2) {
                return Arr::multiArrayToString($val2);
            }, $val1);
        }, $firstTable);

        // name, ref_num, dhv_ref_num
        $caseInfoArray = Arr::collectTableRows($caseInfoTable);

        $patient = $this->getPatient(current($caseInfoArray[self::PATIENT_NAME]));

        return $accident;
    }

    private function getAssistant($assistantStr = '')
    {
        $title = $comment = '';
        // first caps lock it's a title all other it is
        if (preg_match('/^([A-Z ]+)(.*)$/', $assistantStr, $matches)) {
            $title = ucfirst(strtolower($matches[1]));
            $comment = trim($matches[2], '., ');
        }

        $assistant = Assistant::firstOrNew(['title' => $title]);

        if (empty($assistant->comment) && $comment){
            $assistant->comment = $comment;
            $assistant->save();
        }

        return $assistant->id;
    }

    private function getPatient($patientStr = '')
    {
        list($name, $birthday) = explode(',', $patientStr);

        return Patient::firstOrCreate([
            'name' => trim(ucfirst(strtolower($name))),
            'birthday' => strtotime($birthday)
        ]);
    }
}
