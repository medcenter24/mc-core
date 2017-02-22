<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Accident that needs Doctor involvement
 *
 * Class DoctorAccident
 * @package App
 */
class DoctorAccident extends Model
{
    use SoftDeletes;

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SIGNED = 'signed';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_CLOSED = 'closed';

    protected $fillable = ['city_id', 'status', 'doctor_id', 'diagnose', 'accident_statusable_id'];
    protected $visible = ['city_id', 'status', 'doctor_id', 'diagnose', 'accident_statusable_id'];

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
        return $this->belongsToMany(Diagnostic::class);
    }

    /**
     * Assignment from the Doctor_Accident to the status action with comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function accidentStatusable()
    {
        return $this->morphMany(AccidentStatusable::class, 'statusable');
    }
}
