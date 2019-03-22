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
