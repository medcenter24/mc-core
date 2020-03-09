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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Entity\Form;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\FormRequest;
use medcenter24\mcCore\App\Services\Entity\FormService;
use medcenter24\mcCore\App\Transformers\FormTransformer;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormsController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new FormTransformer();
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Form::class;
    }

    protected function getRequestClass(): string
    {
        return FormRequest::class;
    }

    /**
     * @param $formId
     * @param $srcId
     * @return BinaryFileResponse
     */
    public function pdf($formId, $srcId): BinaryFileResponse
    {
        /** @var Form $form */
        $form = $this->getModelService()->first([FormService::FIELD_ID => $formId]);
        if (!$form) {
            $this->response->errorNotFound();
        }
        $source = call_user_func([$form->formable_type, 'findOrFail'], $srcId);
        return response()->download($this->getModelService()->getPdfPath($form, $source));
    }

    /**
     * @param $formId
     * @param $srcId
     * @param FormService $formService
     * @return JsonResponse
     * @throws InconsistentDataException
     */
    public function html($formId, $srcId, FormService $formService): JsonResponse
    {
        /** @var Form $form */
        $form = $this->getModelService()->first([FormService::FIELD_ID => $formId]);
        if (!$form) {
            $this->response->errorNotFound();
        }
        $source = call_user_func([$form->formable_type, 'findOrFail'], $srcId);
        return response()->json(['data' => $formService->getHtml($form, $source)]);
    }

    /**
     * @inheritDoc
     */
    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(FormService::class);
    }
}
