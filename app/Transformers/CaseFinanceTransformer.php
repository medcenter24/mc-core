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

namespace App\Transformers;


use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package App\Transformers
 */
class CaseFinanceTransformer extends TransformerAbstract
{
    /**
     * @param \stdClass $obj
     * @return array
     */
    public function transform (\stdClass $obj) : array
    {
        $result = [];
        $obj->collection->each(function (Collection $item) use (&$result) {
            $payment = $item->get('payment');
            $result[] = [
                'type' => $item->get('type'),
                'loading' => false,
                'payment' => $payment ? (new PaymentTransformer())->transform($payment) : false,
                'currency' => $item->get('currency') ? (new FinanceCurrencyTransformer())->transform($item->get('currency')) : null,
                'formula' => $item->get('formulaView'),
                'calculatedValue' => $item->get('calculatedValue'),
            ];
        });

        return $result;
    }
}
