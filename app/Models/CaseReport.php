<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models;


use App\Accident;
use App\Diagnostic;
use App\DoctorAccident;

class CaseReport
{
    /**
     * @var Accident
     */
    private $accident;

    public function __construct(Accident $accident)
    {
        $this->accident = $accident;
    }

    public function getCustomer()
    {
        return env('customer', 'default');
    }

    public function getProperty($property)
    {
        return trans($this->getCustomer() . '.report.' . $property);
    }

    public function floatingLine()
    {
        return $this->getProperty('floating_line');
    }

    public function hasFloatingLine()
    {
        return true;
    }

    public function companyLogoUrl()
    {

    }

    /**
     * @return string HTML
     */
    public function companyTitle()
    {
        return $this->getProperty('company_title');
    }

    /**
     * @return string HTML
     */
    public function companyDescription()
    {
        return $this->getProperty('company_description');
    }

    /**
     * @return string - HTML
     */
    public function companyContacts()
    {
        return $this->getProperty('company_contacts');
    }

    /**
     * @return string
     */
    public function companyInfo()
    {
        return $this->getProperty('company_info');
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->getProperty('title');
    }

    /**
     * @return string
     */
    public function assistantLabel()
    {
        return $this->getProperty('assistant_label');
    }

    /**
     * @return string
     */
    public function assistantTitle()
    {
        return $this->accident->assistant ? $this->accident->assistant->title : '';
    }

    /**
     * @return string
     */
    public function patientLabel()
    {
        return $this->getProperty('patient_label');
    }

    /**
     * @return string
     */
    public function assistantRefNumLabel()
    {
        return $this->getProperty('assistant_ref_num_label');
    }

    /**
     * @return string
     */
    public function refNumLabel()
    {
        return $this->getProperty('ref_num_label');
    }

    /**
     * @return string
     */
    public function patientName()
    {
        return $this->accident->patient ? $this->accident->patient->name : '';
    }

    /**
     * @return string
     */
    public function patientHasBirthDate()
    {
        return $this->accident->patient && !empty($this->accident->patient->birthday);
    }

    /**
     * @return string
     */
    public function patientBirthday()
    {
        return $this->patientHasBirthDate() ? $this->accident->patient->birthday : '';
    }

    /**
     * @return string
     */
    public function assistantRefNum()
    {
        return $this->accident->assistant_ref_num;
    }

    /**
     * @return string
     */
    public function refNum()
    {
        return $this->accident->ref_num;
    }

    /**
     * @return string
     */
    public function symptomsLabel()
    {
        return $this->getProperty('symptoms_label');
    }

    /**
     * @return string
     */
    public function symptoms()
    {
        return $this->accident->symptoms;
    }

    /**
     * @return string
     */
    public function surveysLabel()
    {
        return $this->getProperty('surveys_label');
    }

    /**
     * @return string
     */
    public function surveys()
    {
        return $this->accident->surveys()->get()->implode('title', ' ');
    }

    /**
     * @return bool
     */
    public function hasInvestigation()
    {
        return $this->accident->caseable instanceof DoctorAccident && !empty($this->accident->caseable->investigation);
    }

    /**
     * @return string
     */
    public function investigation()
    {
        return $this->hasInvestigation() ? $this->accident->caseable->investigation : '';
    }

    /**
     * @return string
     */
    public function investigationLabel()
    {
        return $this->getProperty('investigation_label');
    }

    /**
     * @return string
     */
    public function diagnoseLabel()
    {
        return $this->getProperty('diagnose_label');
    }

    /**
     * @return bool
     */
    public function hasDiagnose()
    {
        return $this->accident->caseable instanceof DoctorAccident && !empty($this->accident->caseable->recommendation);
    }

    /**
     * @return string
     */
    public function diagnose()
    {
        return $this->hasDiagnose() ? $this->accident->caseable->recommendation : '';
    }

    /**
     * @return string
     */
    public function diagnosticTitle()
    {
        return $this->getProperty('diagnostic_title');
    }

    /**
     * @return string
     */
    public function diagnosticDescription()
    {
        return $this->getProperty('diagnostic_description');
    }

    /**
     * @return Diagnostic[]
     */
    public function diagnostics()
    {
        return $this->accident->diagnostics()->orderBy('title')->get();
    }

    /**
     * @return bool
     */
    public function hasDoctor()
    {
        return $this->accident->caseable instanceof DoctorAccident && $this->accident->caseable->doctor_id;
    }

    /**
     * @return string
     */
    public function doctorName()
    {
        return $this->hasDoctor() ? $this->accident->caseable->doctor->name : '';
    }

    /**
     * @return string
     */
    public function doctorBoardNumSeparator()
    {
        return $this->getProperty('doctor_board_num_separator');
    }

    /**
     * @return string
     */
    public function doctorBoardNum()
    {
        return $this->hasDoctor() ? $this->accident->caseable->doctor->medical_board_num : '';
    }
}
