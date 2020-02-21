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


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocator;
use medcenter24\mcCore\App\Services\DoctorAccidentService;

/**
 * Accident that needs Doctor involvement
 *
 * Class DoctorAccident
 * @package App
 */
class DoctorAccident extends AccidentAbstract
{
    protected $dates = DoctorAccidentService::DATE_FIELDS;

    protected $fillable = DoctorAccidentService::FILLABLE;
    protected $visible = ['doctor_id', 'recommendation', 'investigation', 'visit_time'];

    public static function boot(): void
    {
        parent::boot();
        self::saved(static function (DoctorAccident $doctorAccident) {
            $serviceLocator = ServiceLocator::instance();
            $serviceLocator->get(AccidentService::class)->updateDoctorAccidentStatus($doctorAccident);
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

    /**
     * Selected by doctor diagnostics
     */
    public function diagnostics(): MorphToMany
    {
        return $this->morphToMany(Diagnostic::class, 'diagnosticable');
    }

    /**
     * Each DoctorAccident is able to has own services, created by a doctor
     * but by default it could be defined by the director
     *
     * @return MorphToMany
     */
    public function services(): MorphToMany
    {
        return $this->morphToMany(DoctorService::class, 'doctor_serviceable');
    }

    /**
     * As same as serviceable()
     * each doctorAccident is able to has his own survey
     * but by default it could be defined by the director
     *
     * @return MorphToMany
     */
    public function surveys(): MorphToMany
    {
        return $this->morphToMany(DoctorSurvey::class, 'doctor_surveable');
    }
}
