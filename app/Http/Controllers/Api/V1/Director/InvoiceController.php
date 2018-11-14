<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;


use App\FinanceCurrency;
use App\Form;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\InvoiceRequest;
use App\Invoice;
use App\Payment;
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
        $invoice = Invoice::findOrFail($id);
        if (!$invoice) {
            $this->response->errorNotFound();
        }

        $invoice->title= $request->json('title', '');
        $invoice->type = $request->json('type', '');
        $invoice->status = $request->json('status', 'new');
        $invoice->save();

        $price = $request->json('price', 0);
        $this->assignPayment($invoice, $price);

        $this->assignInvoiceTypeResource($invoice, $request);

        $transformer = $this->getDataTransformer();
        return $this->response->accepted(null, $transformer->transform($invoice));
    }

    private function assignInvoiceTypeResource(Invoice $invoice, $request)
    {
        if ($invoice->type === 'form' && $request->json('formId', false)) {
            $form = Form::findOrFail($request->json('formId'));
            if ($form && !$invoice->forms()->where('id', $form->id)->count()) {
                $invoice->forms()->detach();
                $invoice->forms()->attach($form);
            }
        } elseif ($invoice->type === 'file' && $request->json('fileId', false)) {
            $file = Upload::findOrFail($request->json('fileId'));
            if ($file && !$invoice->uploads()->where('id', $file->id)->count()) {
                $invoice->uploads()->delete();
                $invoice->uploads()->save($file);
            }
        }
    }

    public function store(InvoiceRequest $request)
    {
        $invoice = Invoice::create([
            'title' => $request->json('title', ''),
            'type' => $request->json('type', ''),
            'created_by' => $this->user()->id,
            'status' => $request->json('status', 'new'),
        ]);

        $price = $request->json('price', 0);
        $this->assignPayment($invoice, $price);

        $this->assignInvoiceTypeResource($invoice, $request);

        $transformer = $this->getDataTransformer();
        return $this->response->created(null, $transformer->transform($invoice));
    }

    private function assignPayment(Invoice &$invoice, $price = 0, FinanceCurrency $currency = null)
    {
        $payment = Payment::create([
            'value' => $price,
            'currency_id' => 0,
        ]);
        $invoice->payment = $payment;
        $invoice->save();
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