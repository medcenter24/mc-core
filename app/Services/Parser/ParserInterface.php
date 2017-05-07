<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace app\Services\Parser;


class ParserInterface extends ConfigurableInterface
{
    /**
     * boolean parse attributes or no
     * <tag attr="val">
     */
    const CONFIG_WITH_ATTRIBUTES = 'with_attributes';

    /**
     * boolean all text from the texts nodes will be stripped
     */
    const CONFIG_STRIP_TEXT = 'strip_text';
}
