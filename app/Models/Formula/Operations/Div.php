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

namespace medcenter24\mcCore\App\Models\Formula\Operations;


use medcenter24\mcCore\App\Contract\Formula\Variable;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;

class Div extends AbstractOperation
{
    protected $weight = 1;

    /**
     * Div constructor.
     * @param Variable $var
     * @throws FormulaException
     */
    public function __construct(Variable $var)
    {
        if ($var->getResult() === 0) {
            throw new FormulaException('Divide by zero');
        }
        parent::__construct($var);
    }

    /**
     * @return string
     */
    public function getLeftSignView(): string
    {
        return ' / ';
    }

    public function runOperation($result)
    {
        return $result / $this->variable->getResult();
    }
}
