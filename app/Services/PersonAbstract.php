<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


abstract class PersonAbstract
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_NONE = 'none';

    public function grades()
    {
        return collect([
            self::GENDER_MALE,
            self::GENDER_FEMALE,
            self::GENDER_NONE,
        ]);
    }
}
