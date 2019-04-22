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

namespace medcenter24\mcCore\App;


use medcenter24\mcCore\App\Services\AccidentStatusesService;

class HospitalAccident extends AccidentAbstract
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['hospital_id', 'hospital_guarantee_id', 'hospital_invoice_id'];
    protected $visible = ['hospital_id', 'hospital_guarantee_id', 'hospital_invoice_id'];

    public static function boot()
    {
        parent::boot();

        self::saved( function (HospitalAccident $hospitalAccident) {
            (new AccidentStatusesService())->updateHospitalAccidentStatus($hospitalAccident);
        } );
    }

    /**
     * Hospital of this accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospitalGuarantee()
    {
        return $this->belongsTo(FormReport::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospitalInvoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
