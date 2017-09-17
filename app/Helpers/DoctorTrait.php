<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers;


trait DoctorTrait
{
    private $isDoctor = false;

    public function markAsDoctor()
    {
        $this->isDoctor = true;
    }

    public function isDoctor()
    {
        return $this->isDoctor;
    }
}
