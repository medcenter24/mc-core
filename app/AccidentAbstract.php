<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccidentAbstract extends Model
{
    use SoftDeletes;

    public function accident()
    {
        return $this->morphOne(Accident::class, 'caseable');
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
     * Selected by doctor diagnostics
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function diagnostics()
    {
        return $this->morphToMany(Diagnostic::class, 'diagnosticable');
    }

    /**
     * Each DoctorAccident is able to has own services, created by a doctor
     * but by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function services()
    {
        return $this->morphToMany(DoctorService::class, 'doctor_serviceable');
    }

    /**
     * As same as serviceable()
     * each doctorAccident is able to has his own survey
     * but by default it could be defined by the director
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function surveys()
    {
        return $this->morphToMany(DoctorSurvey::class, 'doctor_surveable');
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


}
