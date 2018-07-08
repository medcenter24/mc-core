<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;


use App\Form;
use App\Http\Controllers\ApiController;
use App\Transformers\FormTransformer;
use Illuminate\Http\Request;

class FormsController extends ApiController
{
    protected function getDataTransformer()
    {
        return new FormTransformer();
    }

    protected function getModelClass()
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

    public function store(Request $request)
    {
        $form = Form::create([
            'title' => $request->json('title', ''),
            'template' => $request->json('template', ''),
            'variables' => $request->json('variables', ''),
            'formable_type' => $request->json('formableType', ''),
        ]);
        $transformer = new FormTransformer();
        return $this->response->created(null, $transformer->transform($form));
    }

    public function update($id, Request $request)
    {
        $form = Form::findOrFail($id);
        $form->title = $request->json('title', '');
        $form->template = $request->json('template', '');
        $form->variables = $request->json('variables', '');
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
}
