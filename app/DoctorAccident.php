<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use App\Services\AccidentStatusesService;
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
