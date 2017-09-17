<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace app\Services\Parser\Dhv24;


use App\Support\Core\Configurable;

abstract class Dhv24ParserProvider extends Configurable
{


    /**
     * Insure that we have exactly file which we expecting
     * For that will used default structure of the file
     * @return mixed
     */
    abstract public function checkFileFormatPoints();
}
