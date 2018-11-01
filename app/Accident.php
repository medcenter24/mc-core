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
    protected $fillable = [
        'created_by',
        'parent_id',
        'patient_id',
        'accident_type_id',
        'accident_status_id',
        'assistant_id',
        'assistant_ref_num',
        'assistant_invoice_id',
        'assistant_guarantee_id',
        'caseable_id',
        'caseable_type',
        'ref_num',
        'title',
        'city_id',
        'address',
        'contacts',
        'symptoms',
        'handling_time',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'handling_time',
    ];

    protected $visible = [
        'id',
        'parent_id',
        'patient_id',
        'accident_type_id',
        'accident_status_id',
        'assistant_id',
        'assistant_ref_num',
        'ref_num',
        'title',
        'city_id',
        'address',
        'contacts',
        'symptoms',
        'handling_time',
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
     * Assistant company
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistant()
    {
        return $this->belongsTo(Assistant::class);
    }

    /**
     * Patient from the accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function type()
    {
        return $this->belongsTo(AccidentType::class, 'accident_type_id');
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function surveys()
    {
        return $this->morphToMany(DoctorSurvey::class, 'doctor_surveable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function caseable()
    {
        return $this->morphTo();
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accidentStatus()
    {
        return $this->belongsTo(AccidentStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistantInvoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistantGuarantee()
    {
        return $this->belongsTo(Invoice::class);
    }
}
