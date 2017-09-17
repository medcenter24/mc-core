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
 * Guarantee letter that MyCompany send to the hospital for the patient bill
 *
 * Class Guarantee
 * @package App
 */
class Guarantee extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'text', 'status'];
    protected $visible = ['title', 'text', 'status'];

    /**
     * Guarantee body for the hospital stored as a FormReport element
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function formReport()
    {
        return $this->belongsTo(FormReport::class);
    }
}
