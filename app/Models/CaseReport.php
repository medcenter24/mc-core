<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models;


use App\Accident;
use App\Company;
use App\Diagnostic;
use App\DoctorAccident;
use App\Services\LogoService;
use App\Services\SignatureService;
use Carbon\Carbon;

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

    public function uniqueIdentifier()
    {
        return $this->accident->ref_num . '_' . $this->accident->updated_at->format('Ymd_His');
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
        // todo assignment should has company, implementation of it in the future
        return Company::first()->getFirstMediaUrl(LogoService::FOLDER, 'thumb_250');
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
    public function assistantAddress()
    {
        return $this->accident->assistant ? $this->accident->assistant->comment : '';
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
        return $this->patientHasBirthDate() ? Carbon::createFromFormat('Y-m-d', $this->accident->patient->birthday)->format(config('date.dateFormat')) : '';
    }

    /**
     * @return string
     */
    public function currency()
    {
        return $this->getProperty('currency');
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
    public function isDoctorAccident()
    {
        return $this->accident->caseable instanceof DoctorAccident;
    }

    /**
     * @return bool
     */
    public function hasInvestigation()
    {
        return $this->isDoctorAccident() && !empty($this->accident->caseable->investigation);
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
        return $this->isDoctorAccident() && !empty($this->accident->caseable->recommendation);
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
        return $this->isDoctorAccident() && $this->accident->caseable->doctor_id;
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

    /**
     * @return string
     */
    public function serviceTitle()
    {
        return $this->getProperty('service_title');
    }

    /**
     * @return string
     */
    public function serviceDescription()
    {
        return $this->getProperty('service_description');
    }

    /**
     * @return string
     */
    public function serviceFooterDescription()
    {
        return $this->getProperty('service_footer_description');
    }

    /**
     * @return string
     */
    public function serviceFooterTitle()
    {
        return $this->getProperty('service_footer_title');
    }

    /**
     * @return Service[]
     */
    public function services()
    {
        return $this->accident->services()->orderBy('title')->get();
    }

    /**
     * @return float
     */
    public function totalAmount()
    {
        return $this->accident->fixed_income ? $this->accident->caseable_cost : $this->services()->sum('price');
    }

    /**
     * @return string
     */
    public function visitInfoTitle()
    {
        return $this->getProperty('visit_info_title');
    }

    /**
     * @return string
     */
    public function visitInfoPlace()
    {
        return $this->getProperty('visit_info_place');
    }

    /**
     * @return string
     */
    public function visitCountry()
    {
        return $this->getProperty('visit_country');
    }

    /**
     * @return string
     */
    public function visitTime()
    {
        return $this->isDoctorAccident() ? $this->accident->caseable->visit_time->format('H:i') : '';
    }

    /**
     * @return string
     */
    public function visitDate()
    {
        return $this->isDoctorAccident() ? $this->accident->caseable->visit_time->format('d.m.Y') : '';
    }

    /**
     * @return string
     */
    public function city()
    {
        return $this->accident->city_id ? $this->accident->city->title : '';
    }

    /**
     * @return string
     */
    public function bankTitle()
    {
        return $this->getProperty('bank_title');
    }

    /**
     * @return string
     */
    public function bankAddress()
    {
        return $this->getProperty('bank_address');
    }

    /**
     * @return string
     */
    public function bankDetailsLabel()
    {
        return $this->getProperty('bank_details_label');
    }

    /**
     * @return string
     */
    public function bankHolderLabel()
    {
        return $this->getProperty('bank_holder_label');
    }

    /**
     * @return string
     */
    public function bankHolder()
    {
        return $this->getProperty('bank_holder');
    }

    /**
     * @return string
     */
    public function bankIbanLabel()
    {
        return $this->getProperty('bank_iban_label');
    }

    /**
     * @return string
     */
    public function bankIban()
    {
        return $this->getProperty('bank_iban');
    }

    /**
     * @return string
     */
    public function bankSwiftLabel()
    {
        return $this->getProperty('bank_swift_label');
    }

    /**
     * @return string
     */
    public function bankSwift()
    {
        return $this->getProperty('bank_swift');
    }

    public function stampUrl()
    {
        // todo assignment should has company, implementation of it in the future
        return Company::first()->hasMedia(SignatureService::FOLDER)
            ? base64_encode(file_get_contents(Company::first()->getFirstMediaPath(SignatureService::FOLDER, 'thumb_300x100')))
            : '';
    }
}
