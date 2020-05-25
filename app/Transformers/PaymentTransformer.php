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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Transformers;

use medcenter24\mcCore\App\Services\Entity\PaymentService;

class PaymentTransformer extends AbstractTransformer
{
    protected function getMap(): array
    {
        return [
            PaymentService::FIELD_ID,
            'createdBy' => PaymentService::FIELD_CREATED_BY,
            PaymentService::FIELD_VALUE,
            'currencyId' => PaymentService::FIELD_CURRENCY_ID,
            PaymentService::FIELD_FIXED,
            PaymentService::FIELD_DESCRIPTION,
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            PaymentService::FIELD_ID => self::VAR_INT,
            PaymentService::FIELD_CREATED_BY => self::VAR_INT,
            PaymentService::FIELD_VALUE => self::VAR_FLOAT,
            PaymentService::FIELD_FIXED => self::VAR_BOOL,
            PaymentService::FIELD_CURRENCY_ID => self::VAR_INT,
        ];
    }
}
