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

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\DiagnosticCategory;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DiagnosticCategoryUpdate;
use medcenter24\mcCore\App\Transformers\CategoryTransformer;
use League\Fractal\TransformerAbstract;

class CategoriesController extends ModelApiController
{

    protected function getDataTransformer(): TransformerAbstract
    {
        return new CategoryTransformer();
    }

    protected function getModelClass(): string
    {
        return DiagnosticCategory::class;
    }

    public function show($id): Response
    {
        $category = DiagnosticCategory::findOrFail($id);
        return $this->response->item($category, new CategoryTransformer());
    }

    public function update($id, DiagnosticCategoryUpdate $request): Response
    {
        $category = DiagnosticCategory::findOrFail($id);
        $category->title = $request->json('title', 'Category ' . $category->id);
        $category->save();

        return $this->response->item($category, new CategoryTransformer());
    }

    public function store(DiagnosticCategoryUpdate $request): Response
    {
        $category = DiagnosticCategory::create([
            'title' => $request->json('title', 'NewCategory')
        ]);
        $transformer = new CategoryTransformer();
        return $this->response->created(null, $transformer->transform($category));
    }
}
