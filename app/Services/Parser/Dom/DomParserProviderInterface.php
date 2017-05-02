<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace app\Services\Parser\Dom;

/**
 * Provided correct parser for dom
 *
 * Interface ProviderInterface
 * @package app\Services\Parser\Dom
 */
interface DomParserProviderInterface
{
    /**
     * Match that dom include all check points for that provider
     *
     * @return mixed
     */
    public function match();
}
