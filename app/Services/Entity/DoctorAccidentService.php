<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\DoctorAccident;

class DoctorAccidentService extends AbstractModelService
{

    public const FIELD_DOCTOR_ID = 'doctor_id';
    public const FIELD_RECOMMENDATION = 'recommendation';
    public const FIELD_INVESTIGATION = 'investigation';
    public const FIELD_VISIT_TIME = 'visit_time';

    public const DATE_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_DELETED_AT,
        self::FIELD_UPDATED_AT,
        self::FIELD_VISIT_TIME,
    ];

    public const FILLABLE = [
        self::FIELD_DOCTOR_ID,
        self::FIELD_RECOMMENDATION,
        self::FIELD_INVESTIGATION,
        self::FIELD_VISIT_TIME,
    ];

    public const UPDATABLE = [
        self::FIELD_DOCTOR_ID,
        self::FIELD_RECOMMENDATION,
        self::FIELD_INVESTIGATION,
        self::FIELD_VISIT_TIME,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_DOCTOR_ID,
        self::FIELD_RECOMMENDATION,
        self::FIELD_INVESTIGATION,
        self::FIELD_VISIT_TIME,
    ];

    public function getClassName(): string
    {
        return DoctorAccident::class;
    }

    #[ArrayShape([
        self::FIELD_DOCTOR_ID => "int",
        self::FIELD_RECOMMENDATION => "string",
        self::FIELD_INVESTIGATION => "string"
    ])]
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_DOCTOR_ID      => 0,
            self::FIELD_RECOMMENDATION => '',
            self::FIELD_INVESTIGATION  => '',
        ];
    }

    public function getSortedServices(int $accidentId): Collection
    {
        $queryBuilder = DB::table('accidents');
        $queryBuilder->where('accidents.id', '=', $accidentId);
        $queryBuilder->where('accidents.caseable_type', '=', DoctorAccident::class);
        $queryBuilder->join('doctor_accidents as da',
            static function (JoinClause $joiner) {
                $joiner->on('accidents.caseable_id', '=', 'da.id');
            });
        $queryBuilder->join('serviceables as sp',
            static function (JoinClause $joiner) {
                $joiner->on('da.id', '=', 'sp.serviceable_id');
                $joiner->where('sp.serviceable_type', '=', DoctorAccident::class);
            });
        $queryBuilder->join('services as s',
            static function (JoinClause $joiner) {
                $joiner->on('s.id', '=', 'sp.service_id');
            });
        $queryBuilder->select('s.*');
        $queryBuilder->addSelect(DB::raw('sp.sort AS "sort"'));
        $queryBuilder->orderBy('sp.sort');
        return $queryBuilder->get();
    }

    public function getSortedDiagnostics(int $accidentId): Collection
    {
        $queryBuilder = DB::table('accidents');
        $queryBuilder->where('accidents.id', '=', $accidentId);
        $queryBuilder->where('accidents.caseable_type', '=', DoctorAccident::class);
        $queryBuilder->join('doctor_accidents as da',
            static function (JoinClause $joiner) {
                $joiner->on('accidents.caseable_id', '=', 'da.id');
            });
        $queryBuilder->join('diagnosticables as dp',
            static function (JoinClause $joiner) {
                $joiner->on('da.id', '=', 'dp.diagnosticable_id');
                $joiner->where('dp.diagnosticable_type', '=', DoctorAccident::class);
            });
        $queryBuilder->join('diagnostics as d',
            static function (JoinClause $joiner) {
                $joiner->on('d.id', '=', 'dp.diagnostic_id');
            });
        $queryBuilder->select('d.*');
        $queryBuilder->addSelect(DB::raw('dp.sort AS "sort"'));
        $queryBuilder->orderBy('dp.sort');
        return $queryBuilder->get();
    }

    public function getSortedSurveys(int $accidentId): Collection
    {
        $queryBuilder = DB::table('accidents');
        $queryBuilder->where('accidents.id', '=', $accidentId);
        $queryBuilder->where('accidents.caseable_type', '=', DoctorAccident::class);
        $queryBuilder->join('doctor_accidents as da',
            static function (JoinClause $joiner) {
                $joiner->on('accidents.caseable_id', '=', 'da.id');
            });
        $queryBuilder->join('surveables as sp',
            static function (JoinClause $joiner) {
                $joiner->on('da.id', '=', 'sp.surveable_id');
                $joiner->where('sp.surveable_type', '=', DoctorAccident::class);
            });
        $queryBuilder->join('surveys as s',
            static function (JoinClause $joiner) {
                $joiner->on('s.id', '=', 'sp.survey_id');
            });
        $queryBuilder->select('s.*');
        $queryBuilder->addSelect(DB::raw('sp.sort AS "sort"'));
        $queryBuilder->orderBy('sp.sort');
        return $queryBuilder->get();
    }
}
