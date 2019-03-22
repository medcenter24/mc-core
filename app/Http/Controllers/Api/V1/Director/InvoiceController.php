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


use App\FinanceCurrency;
use App\Form;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\InvoiceRequest;
use App\Invoice;
use App\Payment;
use App\Services\CurrencyService;
use App\Transformers\FormTransformer;
use App\Transformers\InvoiceTransformer;
use App\Transformers\UploadedFileTransformer;
use App\Upload;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class InvoiceController extends ApiController
{

    /**
     * @return InvoiceTransformer|\League\Fractal\TransformerAbstract
     */
    protected function getDataTransformer(): TransformerAbstract
    {
        return new InvoiceTransformer();
    }

    protected function getModelClass(): string
    {
        return Invoice::class;
    }

    /**
     * @param $id
     * @param InvoiceRequest $request
     * @param CurrencyService $currencyService
     * @return \Dingo\Api\Http\Response
     */
    public function update($id, InvoiceRequest $request, CurrencyService $currencyService): Response
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
        $this->assignPayment($invoice, $price, $currencyService->getDefaultCurrency());

        $this->assignInvoiceTypeResource($invoice, $request);

        $transformer = $this->getDataTransformer();
        return $this->response->accepted(null, $transformer->transform($invoice));
    }

    private function assignInvoiceTypeResource(Invoice $invoice, $request): void
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

    public function store(InvoiceRequest $request, CurrencyService $currencyService): Response
    {
        $invoice = Invoice::create([
            'title' => $request->json('title', ''),
            'type' => $request->json('type', ''),
            'created_by' => $this->user()->id,
            'status' => $request->json('status', 'new'),
        ]);

        $price = $request->json('price', 0);
        $this->assignPayment($invoice, $price, $currencyService->getDefaultCurrency());

        $this->assignInvoiceTypeResource($invoice, $request);

        $transformer = $this->getDataTransformer();
        return $this->response->created(null, $transformer->transform($invoice));
    }

    private function assignPayment(Invoice $invoice, int $price, FinanceCurrency $currency): void
    {
        $payment = Payment::create([
            'value' => $price,
            'currency_id' => $currency->id,
            'fixed' => 0,
            'description' => '',
        ]);
        $invoice->payment()->associate($payment);
        $invoice->save();
    }

    /**
     * Getting form of the invoice
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function form($id): Response
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::findOrFail($id);
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
    public function file($id): Response
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::findOrFail($id);
        $upload = $invoice->uploads()->first();
        if (!$invoice->uploads()->count()) {
            return $this->response->noContent();
        }
        return $this->response->item($upload, new UploadedFileTransformer());
    }
}