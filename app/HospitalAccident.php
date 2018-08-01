<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App;


class HospitalAccident extends AccidentAbstract
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['hospital_id', 'hospital_guarantee_id', 'hospital_invoice_id', 'assistant_invoice_id', 'assistant_guarantee_id', 'assistant_paid'];
    protected $visible = ['hospital_id', 'hospital_guarantee_id', 'hospital_invoice_id', 'assistant_invoice_id', 'assistant_guarantee_id', 'assistant_paid'];

    /**
     * Hospital of this accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospitalGuarantee()
    {
        return $this->belongsTo(FormReport::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospitalInvoice()
    {
        return $this->belongsTo(Invoice::class);
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
        return $this->belongsTo(FormReport::class);
    }
}
