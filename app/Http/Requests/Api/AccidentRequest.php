<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Requests\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use medcenter24\mcCore\App\Entity\Accident;
use Illuminate\Validation\Rule;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Transformers\CaseAccidentTransformer;

class AccidentRequest extends JsonRequest
{
    private function getFields($attribute, $value, array $parameters, $validator): array
    {
        array_shift( $parameters );

        // start building the conditions
        $fields = [ $attribute => $value ]; // current field, company_code in your case

        // iterates over the other parameters and build the conditions for all the required fields
        while ( $field = array_shift( $parameters ) ) {
            $fields[ $field ] = $this->get($field);
            if (!$fields[ $field ] && array_key_exists($field, $validator->getData())) {
                $fields[ $field ] = $validator->getData()[$field];
            }
        }
        return $fields;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // extends Validator only for this request
        Validator::extend( 'parent_rule', function ( $attribute, $value, $parameters, $validator ) {

            $fields = $this->getFields($attribute, $value, $parameters, $validator);

            $res = true;
            if ($fields['id'] && $fields['parentId']) {
                $res = $fields['id'] === $fields['parentId']
                    ? false // can't be the same
                    : Accident::where('id', $fields['parentId'])->exists();
            }
            return $res;
        }, 'Parent is incorrect' );

        Validator::extend( 'caseable_rule', function ( $attribute, $value, $parameters, $validator ) {

            $fields = $this->getFields($attribute, $value, $parameters, $validator);

            $res = true;
            if ($fields['caseableType'] && $fields['caseableId']) {
                $caseTypeMap = CaseAccidentTransformer::CASE_TYPE_MAP;
                $cl = array_search($fields['caseableType'], $caseTypeMap, true);
                $res = $cl && class_exists($cl) ? $cl::where('id', $fields['caseableId'])->exists() : false;
            }

            return $res;
        }, 'Caseable does not exists' );

        Validator::extend( 'exists_if_set', function ( $attribute, $value, $parameters, $validator ) {

            // remove first parameter and assume it is the table name
            $table = array_shift( $parameters );
            // iterates over the other parameters and build the conditions for all the required fields
            $field = array_shift( $parameters );

            $result = true;
            if ($value) {
                // query the table with all the conditions
                $result = DB::table( $table )->select( DB::raw( 1 ) )->where( $field, $value )->exists();
            }
            return $result; // edited here
        }, 'Is not exists' );

        return [
            'parentId' => 'parent_rule:accidents,id',
            // it is not caseable_type field that is GUI expectations only
            'caseableType' => Rule::in(['', CaseAccidentTransformer::CASE_TYPE_DOCTOR, CaseAccidentTransformer::CASE_TYPE_HOSPITAL]),
            'caseableId' => 'caseable_rule:accidents,caseableType',
            'patientId' => 'exists_if_set:patients,id',
            'accidentTypeId' => 'exists_if_set:accident_types,id',
            'accidentStatusId' => 'exists_if_set:accident_statuses,id',
            'assistantId' => 'exists_if_set:assistants,id',
            'assistantInvoiceId' => 'exists_if_set:invoices,id',
            'assistantGuaranteeId' => 'exists_if_set:uploads,id',
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
