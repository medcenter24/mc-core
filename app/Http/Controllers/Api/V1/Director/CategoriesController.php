<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\DiagnosticCategory;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DiagnosticCategoryUpdate;
use App\Transformers\CategoryTransformer;

class CategoriesController extends ApiController
{
    public function index()
    {
        $categories = DiagnosticCategory::orderBy('title')->get();
        return $this->response->collection($categories, new CategoryTransformer());
    }

    public function show($id)
    {
        $category = DiagnosticCategory::findOrFail($id);
        return $this->response->item($category, new CategoryTransformer());
    }

    public function update($id, DiagnosticCategoryUpdate $request)
    {
        $category = DiagnosticCategory::findOrFail($id);
        $category->title = $request->json('title', 'Category ' . $category->id);
        $category->save();

        return $this->response->item($category, new CategoryTransformer());
    }

    public function store(DiagnosticCategoryUpdate $request)
    {
        $category = DiagnosticCategory::create([
            'title' => $request->json('title', 'NewCategory')
        ]);
        $transformer = new CategoryTransformer();
        return $this->response->created(null, $transformer->transform($category));
    }
}
