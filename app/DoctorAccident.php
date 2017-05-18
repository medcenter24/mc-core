<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


/**
 * Accident that needs Doctor involvement
 *
 * Class DoctorAccident
 * @package App
 */
class DoctorAccident extends AccidentAbstract
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SIGNED = 'signed';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_CLOSED = 'closed';

    protected $fillable = ['city_id', 'status', 'doctor_id', 'diagnose', 'accident_status_id', 'investigation', 'visit_time'];
    protected $visible = ['city_id', 'status', 'doctor_id', 'diagnose', 'accident_status_id', 'investigation', 'visit_time'];

    public function accident()
    {
        return $this->morphOne(Accident::class, 'caseable');
    }

    /**
     * Photos of the documents from the patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable');
    }

    /**
     * Selected by doctor diagnostics
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function diagnostics()
    {
        return $this->morphToMany(Diagnostic::class, 'diagnosticable');
    }

    /**
     * Assignment from the Doctor_Accident to the status action with comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function statusHistory()
    {
        return $this->morphMany(AccidentStatusHistory::class, 'historyable');
    }

    /**
     * Each DoctorAccident is able to has own services, created by a doctor
     * but by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function serviceable()
    {
        return $this->morphMany(DoctorService::class, 'serviceable');
    }

    /**
     * As same as serviceable()
     * each doctorAccident is able to has his own survey
     * but by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function surveable()
    {
        return $this->morphMany(DoctorSurvey::class, 'surveable');
    }
}
