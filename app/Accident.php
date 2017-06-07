<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

/**
 * Case|Accident|...
 *
 * Class Accident
 * @package App
 */
class Accident extends AccidentAbstract
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'created_by',
        'parent_id',
        'patient_id',
        'accident_type_id',
        'accident_status_id',
        'assistant_id',
        'assistant_ref_num',
        'caseable_id',
        'caseable_type',
        'ref_num',
        'title',
        'city_id',
        'address',
        'contacts',
        'symptoms',
    ];

    protected $visible = [
        'parent_id',
        'accident_type_id',
        'accident_status_id',
        'ref_num',
        'title',
        'city_id',
        'address',
        'contacts',
        'symptoms',
    ];

    /**
     * Case checkpoints - statuses which could be selected in different order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function checkpoints()
    {
        return $this->belongsToMany(AccidentCheckpoint::class);
    }

    /**
     * Status changes
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function history()
    {
        return $this->morphMany(AccidentStatusHistory::class, 'historyable');
    }

    /**
     * Discount which should be used for this accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Patient from the accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Accident report stored as a FormReport element (which use Assignment form template)
     */
    public function formReport()
    {
        return $this->belongsTo(FormReport::class);
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function surveable()
    {
        return $this->morphMany(DoctorSurvey::class, 'surveable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function caseable()
    {
        return $this->morphTo();
    }
}
