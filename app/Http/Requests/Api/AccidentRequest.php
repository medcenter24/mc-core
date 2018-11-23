<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;


use App\Accident;
use App\DoctorAccident;
use App\HospitalAccident;
use Illuminate\Validation\Rule;

class AccidentRequest extends JsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // extends Validator only for this request
        \Validator::extend( 'parent_rule', function ( $attribute, $value, $parameters, $validator ) {

            // remove first parameter and assume it is the table name
            array_shift( $parameters );

            // start building the conditions
            $fields = [ $attribute => $value ]; // current field, company_code in your case

            // iterates over the other parameters and build the conditions for all the required fields
            while ( $field = array_shift( $parameters ) ) {
                $fields[ $field ] = $this->get($field);
                if (!$fields[ $field ] && key_exists($field, $validator->getData())) {
                    $fields[ $field ] = $validator->getData()[$field];
                }
            }

            $res = true;
            if ($fields['id'] && $fields['parentId']) {
                $res = $fields['id'] == $fields['parentId']
                    ? false // can't be the same
                    : Accident::where('id', $fields['parentId'])->exists();
            }
            return $res;
        }, 'Parent is incorrect' );

        \Validator::extend( 'caseable_rule', function ( $attribute, $value, $parameters, $validator ) {

            // remove first parameter and assume it is the table name
            array_shift( $parameters );

            // start building the conditions
            $fields = [ $attribute => $value ]; // current field, company_code in your case

            // iterates over the other parameters and build the conditions for all the required fields
            while ( $field = array_shift( $parameters ) ) {
                $fields[ $field ] = $this->get($field);
                if (!$fields[ $field ] && key_exists($field, $validator->getData())) {
                    $fields[ $field ] = $validator->getData()[$field];
                }
            }

            $res = true;
            if ($fields['caseableType'] && $fields['caseableId']) {
                $cl = $fields['caseableType'];
                $res = class_exists($cl) ? $cl::where('id', $fields['caseableId'])->exists() : false;
            }

            return $res;
        }, 'Caseable does not exists' );

        \Validator::extend( 'exists_if_set', function ( $attribute, $value, $parameters, $validator ) {

            // remove first parameter and assume it is the table name
            $table = array_shift( $parameters );
            // iterates over the other parameters and build the conditions for all the required fields
            $field = array_shift( $parameters );

            $result = true;
            if ($value) {
                // query the table with all the conditions
                $result = \DB::table( $table )->select( \DB::raw( 1 ) )->where( $field, $value )->exists();
            }
            return $result; // edited here
        }, 'Is not exists' );

        return [
            'parentId' => 'parent_rule:accidents,id',
            'caseableType' => Rule::in(['', HospitalAccident::class, DoctorAccident::class]),
            'caseableId' => 'caseable_rule:accidents,caseableType',
            'patientId' => 'exists_if_set:patients,id',
            'accidentTypeId' => 'exists_if_set:accident_types,id',
            'accidentStatusId' => 'exists_if_set:accident_statuses,id',
            'assistantId' => 'exists_if_set:assistants,id',
            'assistantInvoiceId' => 'exists_if_set:invoices,id',
            'assistantGuaranteeId' => 'exists_if_set:invoices,id',
            'formReportId'  => 'exists_if_set:forms,id',
            'cityId' => 'exists_if_set:cities,id',
            'caseablePaymentId' => 'exists_if_set:payments,id',
            'incomePaymentId' => 'exists_if_set:payments,id',
            'assistantPaymentId' => 'exists_if_set:payments,id',
            'refNum' => 'max:50',
            'title' => 'max:255',
            'address' => 'max:255',
            'handling_time',
            'contacts' => 'max:255',
            // 'symptoms' => '', I don't know if I need to restrict this
        ];
    }
}
