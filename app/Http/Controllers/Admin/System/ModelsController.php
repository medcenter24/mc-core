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

namespace medcenter24\mcCore\App\Http\Controllers\Admin\System;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Services\Core\ModelService;
use ReflectionException;

class ModelsController extends AdminController
{
    /**
     * @return View
     * @throws InconsistentDataException
     */
    public function index(): View
    {
        $this->getMenuService()->markCurrentMenu('5.10');
        return view('admin.system.models.list');
    }

    /**
     * @param ModelService $modelService
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function search(ModelService $modelService): JsonResponse
    {
        return response()->json($modelService->getModels());
    }

    /**
     * @param ModelService $modelService
     * @param Request $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function relations(ModelService $modelService, Request $request): JsonResponse
    {
        return response()->json($modelService->getRelations($request->input('name')));
    }
}
