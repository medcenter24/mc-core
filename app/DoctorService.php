<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;


use App\Helpers\DoctorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Services provided by a Doctor
 *
 * Class DoctorService
 * @package App
 */
class DoctorService extends Model
{
    use SoftDeletes;
    use DoctorTrait;

    protected $fillable = ['title', 'description', 'created_by', 'disease_code'];
    protected $visible = ['title', 'description', 'created_by', 'disease_code'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
