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
    private $doctor;

    public function __construct()
    {
        $this->doctor = Doctor::where('user_id', auth()->user()->id)->get();
    }

    protected function doctor()
    {
        return $this->doctor;
    }
}
