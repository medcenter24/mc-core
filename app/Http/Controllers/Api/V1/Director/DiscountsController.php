<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Discount;
use App\Http\Controllers\ApiController;
use App\Transformers\DiscountTransformer;
use Illuminate\Http\Request;

class DiscountsController extends ApiController
{
    public function index(Request $request)
    {
        $discounts = Discount::orderBy('created_at', 'desc')->get();
        return $this->response->collection($discounts, new DiscountTransformer());
    }
}
