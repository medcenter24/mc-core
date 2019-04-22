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
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Accident that needs Doctor involvement
 *
 * Class DoctorAccident
 * @package App
 */
class DoctorAccident extends AccidentAbstract
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'visit_time',
    ];

    protected $fillable = ['doctor_id', 'recommendation', 'investigation', 'visit_time'];
    protected $visible = ['doctor_id', 'recommendation', 'investigation', 'visit_time'];

    public static function boot(): void
    {
        parent::boot();

        self::saved( function (DoctorAccident $doctorAccident) {
            (new AccidentStatusesService())->updateDoctorAccidentStatus($doctorAccident);
        } );
    }

    /**
     * Doctor of this accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Visit time can be empty
     * (when PR https://github.com/laravel/framework/pull/26525 will be merged, I can delete this)
     * @param array $attributes
     * @return array
     */
    protected function addDateAttributesToArray(array $attributes): array
    {
        foreach ($this->getDates() as $key) {
            if (! isset($attributes[$key])) {
                continue;
            }

            $attributes[$key] = empty ($attributes[$key]) ? '' : $this->serializeDate(
                $this->asDateTime($attributes[$key])
            );
        }

        return $attributes;
    }
}
