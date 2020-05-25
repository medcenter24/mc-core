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

namespace medcenter24\mcCore\App\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocator;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;

class HospitalAccident extends AccidentAbstract
{

    protected $dates = HospitalAccidentService::DATE_FIELDS;
    protected $fillable = HospitalAccidentService::FILLABLE;
    protected $visible = HospitalAccidentService::VISIBLE;

    public static function boot(): void
    {
        parent::boot();

        self::saved(static function (HospitalAccident $hospitalAccident) {
            ServiceLocator::instance()->get(AccidentService::class)
                ->updateHospitalAccidentStatus($hospitalAccident);
        } );
    }

    /**
     * Hospital of this accident
     * @return BelongsTo
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     *
     * @return BelongsTo
     */
    public function hospitalGuarantee()
    {
        return $this->belongsTo(FormReport::class);
    }

    /**
     * @return BelongsTo
     */
    public function hospitalInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
