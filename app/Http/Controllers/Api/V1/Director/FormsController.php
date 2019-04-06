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

namespace App\Http\Controllers\Api\V1\Director;


use App\Form;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FormRequest;
use App\Services\FormService;
use App\Transformers\FormTransformer;
use League\Fractal\TransformerAbstract;

class FormsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new FormTransformer();
    }

    protected function getModelClass(): string
    {
        return Form::class;
    }

    public function index()
    {
        $cities = Form::orderBy('title')->get();
        return $this->response->collection($cities, new FormTransformer());
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $form = Form::findOrFail($id);
        return $this->response->item($form, new FormTransformer());
    }

    public function store(FormRequest $request)
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

    public function update($id, FormRequest $request)
    {
        $form = Form::findOrFail($id);
        $form->title = $request->json('title', '');
        $form->template = $request->json('template', '');
        $form->variables = json_encode($request->json('variables', []));
        $form->formable_type = $request->json('formableType', '');
        $form->save();

        \Log::info('Form updated', [$form, $this->user()]);
        return $this->response->item($form, new FormTransformer());
    }

    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        \Log::info('Form deleted', [$form, $this->user()]);
        $form->delete();
        return $this->response->noContent();
    }

    public function pdf($formId, $srcId, FormService $formService)
    {
        $form = Form::findOrFail($formId);
        $source = call_user_func([$form->formable_type, 'findOrFail'], $srcId);
        return response()->download($formService->getPdfPath($form, $source));
    }

    public function html($formId, $srcId, FormService $formService)
    {
        $form = Form::findOrFail($formId);
        $source = call_user_func([$form->formable_type, 'findOrFail'], $srcId);
        return response()->json(['data' => $formService->getHtml($form, $source)]);
    }
}
