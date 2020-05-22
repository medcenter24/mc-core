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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Forms;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Models\Form\FormVariable;
use medcenter24\mcCore\App\Services\Form\FormVariableService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Transformers\Form\FormVariableTransformer;

class FormsVariablesController extends ApiController
{
    use ServiceLocatorTrait;

    /**
     * @return Response
     */
    public function search(): Response
    {
        /** @var FormVariableService $variableService */
        $variableService = $this->getServiceLocator()->get(FormVariableService::class);
        $variables = collect();
        foreach ($variableService->getAccidentVariables() as $variable) {
            $variables->push(new FormVariable([
                'title' => $variable,
                'key' => $variable,
                'type' => FormVariableService::TYPE_ACCIDENT,
            ]));
        }
        return $this->response->collection($variables, new FormVariableTransformer());
    }
}
