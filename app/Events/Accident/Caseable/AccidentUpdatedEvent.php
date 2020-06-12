<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Events\Accident\Caseable;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentAbstract;

class AccidentUpdatedEvent
{
    /**
     * @var AccidentAbstract|null|Accident
     */
    private ?AccidentAbstract $previousAccident;
    /**
     * @var AccidentAbstract
     */
    private AccidentAbstract $accident;

    public function __construct(
        AccidentAbstract $accident,
        AccidentAbstract $previousAccident = null)
    {
        $this->previousAccident = $previousAccident;
        $this->accident = $accident;
    }

    public function getAccident(): AccidentAbstract
    {
        return $this->accident;
    }

    public function getPreviousAccident(): ?AccidentAbstract
    {
        return $this->previousAccident;
    }
}
