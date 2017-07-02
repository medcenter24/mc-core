<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


class DocumentService
{
    const DISC_IMPORTS = 'documents';
    const CASES_FOLDERS = 'patients';

    const TYPE_PASSPORT = 'passport';
    const TYPE_INSURANCE = 'insurance';

    const TYPES = [self::TYPE_PASSPORT, self::TYPE_INSURANCE];
}
