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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;


use Dingo\Api\Http\Response;
use Illuminate\Http\JsonResponse;
use Log;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Form;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\FormRequest;
use medcenter24\mcCore\App\Services\FormService;
use medcenter24\mcCore\App\Transformers\FormTransformer;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormsController extends ApiController
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

    /**
     * @return Response
     */
    public function index(): Response
    {
        $cities = Form::orderBy('title')->get();
        return $this->response->collection($cities, new FormTransformer());
    }

    /**
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $form = Form::findOrFail($id);
        return $this->response->item($form, new FormTransformer());
    }

    public function store(FormRequest $request): Response
    {
        $form = Form::create([
            'title' => $request->json('title', ''),
            'template' => $request->json('template', ''),
            'variables' => json_encode($request->json('variables', [])),
            'formable_type' => $request->json('formableType', ''),
        ]);
        $transformer = new FormTransformer();
        return $this->response->created(null, $transformer->transform($form));
    }

    public function update($id, FormRequest $request): Response
    {
        $form = Form::findOrFail($id);
        $form->title = $request->json('title', '');
        $form->template = $request->json('template', '');
        $form->formable_type = $request->json('formableType', '');
        $form->save();


        Log::info('Form updated', [$form, $this->user()]);
        return $this->response->item($form, new FormTransformer());
    }

    public function destroy($id): Response
    {
        $form = Form::findOrFail($id);
        Log::info('Form deleted', [$form, $this->user()]);
        $form->delete();
        return $this->response->noContent();
    }

    /**
     * @param $formId
     * @param $srcId
     * @param FormService $formService
     * @return BinaryFileResponse
     * @throws InconsistentDataException
     */
    public function pdf($formId, $srcId, FormService $formService): BinaryFileResponse
    {
        $form = Form::findOrFail($formId);
        $source = call_user_func([$form->formable_type, 'findOrFail'], $srcId);
        return response()->download($formService->getPdfPath($form, $source));
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
        $form = Form::findOrFail($formId);
        $source = call_user_func([$form->formable_type, 'findOrFail'], $srcId);
        return response()->json(['data' => $formService->getHtml($form, $source)]);
    }
}
