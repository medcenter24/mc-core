<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use App\Services\AccidentStatusesService;

/**
 * Case|Accident|...
 *
 * Class Accident
 * @package App
 */
class Accident extends AccidentAbstract
{
    protected $fillable = [
        'parent_id',
        'patient_id',
        'accident_type_id',
        'accident_status_id',
        'assistant_id',
        'assistant_ref_num',
        'assistant_invoice_id',
        'assistant_guarantee_id',
        'form_report_id',
        'city_id',
        'caseable_payment_id',
        'income_payment_id',
        'assistant_payment_id',
        'caseable_id',
        'caseable_type',
        'ref_num',
        'title',
        'address',
        'handling_time',
        'contacts',
        'symptoms',
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
        'assistant_guarantee_id',
        'ref_num',
        'title',
        'city_id',
        'address',
        'contacts',
        'symptoms',
        'handling_time',
        'form_report_id',
    ];

    /**
     * On the save action we need to change status (if it is not status changing action only)
     * @var bool
     */
    private $statusUpdating = false;

    public static function boot()
    {
        parent::boot();

        self::saved( function(Accident $accident) {
            (new AccidentStatusesService())->updateAccidentStatus($accident);
        });
    }

    public function isStatusUpdatingRunned()
    {
        return $this->statusUpdating;
    }

    public function runStatusUpdating()
    {
        $this->statusUpdating = true;
    }

    public function stopStatusUpdating()
    {
        $this->statusUpdating = false;
    }

    /**
     * Payment either to doctor or hospital
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentToCaseable()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Calculated income
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getIncomePayment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Payment from the assistant
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentFromAssistant()
    {
        return $this->belongsTo(Payment::class);
    }

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
        return $this->belongsTo(Upload::class);
    }
}
