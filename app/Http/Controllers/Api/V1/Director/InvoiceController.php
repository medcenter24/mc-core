<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;


use App\Form;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\InvoiceRequest;
use App\Invoice;
use App\Transformers\InvoiceTransformer;
use App\Upload;

class InvoiceController extends ApiController
{

    /**
     * @return InvoiceTransformer|\League\Fractal\TransformerAbstract
     */
    protected function getDataTransformer()
    {
        return new InvoiceTransformer();
    }

    protected function getModelClass()
    {
        return Invoice::class;
    }

    public function update($id, InvoiceRequest $request)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            $this->response->errorNotFound();
        }

        $invoice->title= $request->json('title', '');
        $invoice->type = $request->json('type', '');
        $invoice->price = $request->json('price', 0);
        $invoice->save();

        // assign typed resource
        if ($invoice->type === 'form') {
            $form = Form::find($request->json('formId'));
            $invoice->forms()->detach();
            $invoice->forms()->attach($form);
        } elseif ($invoice->type === 'file') {
            $file = Upload::find($request->json('fileId'));
            $invoice->uploads()->delete();
            $invoice->uploads()->save($file);
        }

        $transformer = $this->getDataTransformer();
        return $this->response->accepted(null, $transformer->transform($invoice));
    }

    public function store(InvoiceRequest $request)
    {
        $doctorService = Invoice::create([
            'title' => $request->json('title', ''),
            'type' => $request->json('type', ''),
            'created_by' => $this->user()->id,
            'price' => $request->json('price', 0),
        ]);
        $transformer = $this->getDataTransformer();
        return $this->response->created(null, $transformer->transform($doctorService));
    }
}