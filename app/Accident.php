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
 * Case|Accident|...
 *
 * Class Accident
 * @package App
 */
class Accident extends Model
{
    use SoftDeletes;

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
        return $this->morphMany(AccidentStatusHistory::class, 'accident_status_histories');
    }

    /**
     * Patient from the accident
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
