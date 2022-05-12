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

namespace medcenter24\mcCore\App\Models\Formula\Variables;

use medcenter24\mcCore\App\Contract\Formula\Variable;

class Integer implements Variable
{
    private int $var;

    public function __construct($var)
    {
        $this->var = (int) $var;
    }

    public function getVar(): int
    {
        return $this->var;
    }

    public function getResult(): int
    {
        return $this->getVar();
    }

    public function varView(): string
    {
        return sprintf('%d', $this->getVar());
    }
}
