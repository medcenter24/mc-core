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
use App\Transformers\FormTransformer;
use App\Transformers\InvoiceTransformer;
use App\Transformers\UploadedFileTransformer;
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
            if ($form) {
                $invoice->forms()->detach();
                $invoice->forms()->attach($form);
            }
        } elseif ($invoice->type === 'file') {
            $file = Upload::find($request->json('fileId'));
            if ($file) {
                $invoice->uploads()->delete();
                $invoice->uploads()->save($file);
            }
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

    /**
     * Getting form of the invoice
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function form($id)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::find($id);
        $form = $invoice->forms()->first();
        if (!$invoice->forms()->count()) {
            return $this->response->noContent();
        }
        return $this->response->item($form, new FormTransformer());
    }

    /**
     * Getting invoices file
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function file($id)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::find($id);
        $upload = $invoice->uploads()->first();
        if (!$invoice->uploads()->count()) {
            return $this->response->noContent();
        }
        return $this->response->item($upload, new UploadedFileTransformer());
    }
}