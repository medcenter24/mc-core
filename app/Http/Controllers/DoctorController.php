<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers;

use App\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * @var Doctor | null
     */
    private static $doctor;

    protected function doctor()
    {
        if (!self::$doctor) {
            self::$doctor = Doctor::where('user_id', auth()->user()->id)->first();
        }
        return self::$doctor;
    }
}
