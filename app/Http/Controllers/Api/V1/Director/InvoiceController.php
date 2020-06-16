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

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\InvoiceRequest;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Http\Requests\Api\InvoiceUpdateRequest;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\FormService;
use medcenter24\mcCore\App\Services\Entity\InvoiceService;
use medcenter24\mcCore\App\Services\Entity\UploadService;
use medcenter24\mcCore\App\Transformers\FormTransformer;
use medcenter24\mcCore\App\Transformers\InvoiceTransformer;
use medcenter24\mcCore\App\Transformers\UploadedFileTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class InvoiceController extends ModelApiController
{
    /**
     * @return InvoiceTransformer|TransformerAbstract
     */
    protected function getDataTransformer(): TransformerAbstract
    {
        return new InvoiceTransformer();
    }

    protected function getInvoiceService(): InvoiceService
    {
        return $this->getServiceLocator()->get(InvoiceService::class);
    }

    protected function getCurrencyService(): CurrencyService
    {
        return $this->getServiceLocator()->get(CurrencyService::class);
    }

    protected function getFormService(): FormService
    {
        return $this->getServiceLocator()->get(FormService::class);
    }

    protected function getUploadService(): UploadService
    {
        return $this->getServiceLocator()->get(UploadService::class);
    }

    /**
     * @inheritDoc
     */
    protected function getModelService(): ModelService
    {
        return $this->getInvoiceService();
    }

    protected function getRequestClass(): string
    {
        return InvoiceRequest::class;
    }

    protected function getUpdateRequestClass(): string
    {
        return InvoiceUpdateRequest::class;
    }

    public function update(int $id, JsonRequest $request): Response
    {
        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceService()->first([InvoiceService::FIELD_ID => $id]);
        if (!$invoice) {
            $this->response->errorNotFound();
        }

        // default model update
        parent::update($id, $request);

        $invoice->refresh();

        // Add payment (for the price)
        $price = (int) $request->get('price', 0);
        try {
            $this->getInvoiceService()->setPrice($invoice, $price, $this->getCurrencyService()->getDefaultCurrency());
        } catch (InconsistentDataException $e) {
            Log::error($e->getMessage(), [$e]);
            $this->response->errorInternal();
        }

        // assign Form or Uploaded file (depends on the invoice type)

        /** @var InvoiceRequest $request */
        $request = call_user_func([$this->getUpdateRequestClass(), 'createFromBase'], $request);
        $request->setContainer(app());
        $request->validateResolved();
        $this->assignInvoiceTypeResource($invoice, $request);

        $transformer = $this->getDataTransformer();
        return $this->response->accepted(null, $transformer->transform($invoice));
    }

    private function assignInvoiceTypeResource(Invoice $invoice, JsonRequest $request): void
    {
        if ($invoice->type === 'form' && $request->json('formId', false)) {

            $form = $this->getFormService()->first([FormService::FIELD_ID => $request->json('formId', 0)]);

            if (!$form) {
                $this->response->errorNotFound();
            }

            if (!$invoice->forms()->where('id', $form->id)->count()) {
                $invoice->forms()->detach();
                $invoice->forms()->attach($form);
            }
        } elseif ($invoice->type === 'file' && $request->json('fileId', false)) {

            $file = $this->getUploadService()
                ->first([UploadService::FIELD_ID => $request->json('fileId', 0)]);

            if (!$file) {
                $this->response->errorNotFound();
            }

            if (!$invoice->uploads()->where('id', $file->id)->count()) {
                $invoice->uploads()->delete();
                $invoice->uploads()->save($file);
            }
        }
    }

    /**
     * @param JsonRequest $request
     * @return Response
     */
    public function store(JsonRequest $request): Response
    {
        /** @var InvoiceRequest $request */
        $request = call_user_func([$this->getRequestClass(), 'createFromBase'], $request);
        $request->validate();

        try {
            /** @var Invoice $invoice */
            $invoice = $this->getInvoiceService()->create([
                'title' => $request->get('title', ''),
                'type' => $request->get('type', InvoiceService::TYPE_UPLOAD),
                'created_by' => $this->user()->id,
                'status' => $request->get('status', 'new'),
            ]);

            $price = (int)$request->get('price', 0);
            $this->getInvoiceService()->setPrice($invoice, $price, $this->getCurrencyService()->getDefaultCurrency());

            $this->assignInvoiceTypeResource($invoice, $request);
        } catch (QueryException | InconsistentDataException $e) {
            Log::error($e->getMessage(), [$e]);
            $this->response->errorInternal();
        }

        return $this->response->created(null, $this->getDataTransformer()->transform($invoice));
    }

    /**
     * Getting form of the invoice
     * @param $id
     * @return Response
     */
    public function form(int $id): Response
    {
        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceService()->first([InvoiceService::FIELD_ID => $id]);

        if (!$invoice) {
            $this->response->errorNotFound();
        }

        $form = $invoice->forms()->first();
        return $this->response->item($form, new FormTransformer());
    }

    /**
     * Getting invoices file
     * @param $id
     * @return Response
     */
    public function file($id): Response
    {
        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceService()->first([InvoiceService::FIELD_ID => $id]);

        if (!$invoice) {
            $this->response->errorNotFound();
        }

        $upload = $invoice->uploads()->first();
        return $this->response->item($upload, new UploadedFileTransformer());
    }
}