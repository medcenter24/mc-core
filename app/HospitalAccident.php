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

    protected $fillable = ['hospital_id', 'guarantee_id', 'invoice_id', 'form_report_id'];
    protected $visible = ['hospital_id', 'guarantee_id', 'invoice_id', 'form_report_id'];

    /**
     * Hospital of this accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function guarantee()
    {
        return $this->belongsTo(Guarantee::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function formReport()
    {
        return $this->belongsTo(FormReport::class);
    }
}
