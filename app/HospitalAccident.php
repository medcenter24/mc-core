<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App;


use App\Services\AccidentStatusesService;

class HospitalAccident extends AccidentAbstract
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['hospital_id', 'hospital_guarantee_id', 'hospital_invoice_id'];
    protected $visible = ['hospital_id', 'hospital_guarantee_id', 'hospital_invoice_id'];

    public static function boot()
    {
        parent::boot();

        self::saved( function (HospitalAccident $hospitalAccident) {
            (new AccidentStatusesService())->updateHospitalAccidentStatus($hospitalAccident);
        } );
    }

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
}
