<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace app\Services\Dhv24;


use app\Services\Parser\Dom\UndefinedDomFormatException;

interface DomReaderInterface
{
    /**
     * determined which parser should be used and
     * which type of case should be created
     *
     * As result could be
     * - doctor Invoice
     * - hospital Invoice
     *
     * @throws UndefinedDomFormatException
     * @return string ['doctorInvoice', 'doctorInvoice_v1', 'hospitalInvoice', 'hospitalInvoice_v1']
     */
    public function getProvider();
}
