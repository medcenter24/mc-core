<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Discount;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DiscountRequest;
use App\Transformers\DiscountTransformer;

class DiscountsController extends ApiController
{
    public function index()
    {
        $discounts = Discount::orderBy('created_at', 'desc')->get();
        return $this->response->collection($discounts, new DiscountTransformer());
    }

    public function show($id)
    {
        $discount = Discount::findOrFail($id);
        return $this->response->item($discount, new DiscountTransformer());
    }

    public function store(DiscountRequest $request)
    {
        $discount = Discount::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
            'operation' => $request->json('operation', ''),
        ]);
        $transformer = new DiscountTransformer();
        return $this->response->created(null, $transformer->transform($discount));
    }

    public function update($id, DiscountRequest $request)
    {
        $discount = Discount::findOrFail($id);
        $discount->title = $request->json('title', '');
        $discount->description = $request->json('description', '');
        $discount->operation = $request->json('operation', '');
        $discount->save();
        \Log::info('Discount updated', [$discount, $this->user()]);
        $this->response->item($discount, new DiscountTransformer());
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        \Log::info('Discount deleted', [$discount, $this->user()]);
        $discount->delete();
        return $this->response->noContent();
    }
}
