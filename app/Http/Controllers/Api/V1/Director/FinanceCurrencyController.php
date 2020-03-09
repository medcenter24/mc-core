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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\FinanceCurrencyRequest;
use medcenter24\mcCore\App\Transformers\FinanceCurrencyTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class FinanceCurrencyController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new FinanceCurrencyTransformer();
    }

    protected function getModelClass(): string
    {
        return FinanceCurrency::class;
    }

    /**
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $currency = FinanceCurrency::findOrFail($id);
        return $this->response->item($currency, new FinanceCurrencyTransformer());
    }

    /**
     * Add new rule
     * @param FinanceCurrencyRequest $request
     * @return Response
     */
    public function store(FinanceCurrencyRequest $request): Response
    {
        $currency = FinanceCurrency::create([
            'title' => $request->json('title', ''),
            'code' => $request->json('code', ''),
            'ico' => $request->json('ico', ''),
        ]);
        $transformer = new FinanceCurrencyTransformer();
        return $this->response->created(url("pages/finance/currencies/{$currency->id}"), $transformer->transform($currency));
    }

    /**
     * Update existing rule
     * @param $id
     * @param FinanceCurrencyRequest $request
     * @return Response
     */
    public function update($id, FinanceCurrencyRequest $request): Response
    {
        $currency = FinanceCurrency::findOrFail($id);
        $currency->title = $request->json('title', '');
        $currency->code = $request->json('code', '');
        $currency->ico = $request->json('ico', '');
        $currency->save();
        return $this->response->item($currency, new FinanceCurrencyTransformer());
    }

    /**
     * Destroy rule
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        $currency = FinanceCurrency::findOrFail($id);
        Log::info('Currency deleted', [$currency, $this->user()]);
        $currency->delete();
        return $this->response->noContent();
    }
}
