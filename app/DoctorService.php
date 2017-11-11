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

    protected $fillable = ['title', 'description', 'price', 'created_by'];
    protected $visible = ['title', 'description', 'price', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
