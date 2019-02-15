<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use App\Services\AccidentStatusesService;

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

    public static function boot()
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
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * by default it could be defined by the director
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function services()
    {
        return $this->morphToMany(DoctorService::class, 'doctor_serviceable');
    }

    /**
     * by default it could be defined by the director
     * Or director maybe want to create new case by them own
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function diagnostics()
    {
        return $this->morphToMany(Diagnostic::class, 'diagnosticable');
    }

    /**
     * by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function surveys()
    {
        return $this->morphToMany(DoctorSurvey::class, 'doctor_surveable');
    }

    /**
     * Visit time can be empty
     * (when PR https://github.com/laravel/framework/pull/26525 will be merged, I can delete this)
     * @param array $attributes
     * @return array
     */
    protected function addDateAttributesToArray(array $attributes)
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
