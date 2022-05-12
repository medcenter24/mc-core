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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Formula;

use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Contract\Formula\Operation;
use Throwable;

class FormulaViewService
{
    /**
     * Returns string where is row performed as a linear string
     * @param FormulaBuilder $formula
     * @return string
     * @throws Throwable
     */
    public function render(FormulaBuilder $formula): string
    {
        $strFormula = '';
        $selfObj = $this;
        // do not change real formula
        $formulaC = clone $formula;
        $formulaC->getFormulaCollection()->each(function (Operation $operation, $key) use (&$strFormula, $selfObj) {
            $var = $operation->getVar();
            if ($var instanceof FormulaBuilder) {
                $part = $selfObj->render($var);
                if ($part && !preg_match('/^[0-9\.\-]+$/', $part)) {
                    $part = '( ' . $part . ' )';
                }
            } else {
                $part = $operation->varView();
            }

            if ($operation->leftSignView((bool) $key)) {
                $strFormula .= $operation->leftSignView();
            }

            $strFormula .= $part ?: '0';
            /*if ($part) {
                $strFormula .= preg_match('/^[0-9\.\-]+$/', $part) ? $part : '( ' . $part . ' )';
            } else {
                $strFormula .= '0';
            }*/

            if ($operation->rightSignView()) {
                $strFormula .= $operation->rightSignView();
            }
        });

        return $strFormula;
    }
}
