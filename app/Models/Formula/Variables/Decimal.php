<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace App\Models\Formula\Variables;


use App\Contract\Formula\Variable;

class Decimal implements Variable
{
    /**
     * @var float
     */
    private $var;

    /**
     * @var int
     */
    private $precision;

    /**
     * Decimal constructor.
     * @param $var
     * @param int $precision
     */
    public function __construct($var, int $precision = null)
    {
        if ($precision === null) {
            $precision = 2;
        }
        $this->var = round((float) $var, $precision);
        $this->precision = $precision;
    }

    public function getVar()
    {
        return $this->var;
    }

    public function getResult()
    {
        return $this->getVar();
    }

    public function varView(): string
    {
        return sprintf('%0.'.$this->precision.'f', $this->getVar());
    }
}
