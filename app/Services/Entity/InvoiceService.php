<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Events\InvoiceChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;

class InvoiceService extends AbstractModelService
{
    public const FIELD_TITLE = 'title';
    public const FIELD_PAYMENT_ID = 'payment_id';
    public const FIELD_CREATED_BY = 'created_by';
    public const FIELD_TYPE = 'type';
    public const FIELD_STATUS = 'status';

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_CREATED_BY,
        self::FIELD_TITLE,
        self::FIELD_PAYMENT_ID,
        self::FIELD_TYPE,
        self::FIELD_STATUS,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_PAYMENT_ID,
        self::FIELD_TYPE,
        self::FIELD_STATUS,
    ];

    public const FILLABLE = [
        self::FIELD_CREATED_BY,
        self::FIELD_TITLE,
        self::FIELD_PAYMENT_ID,
        self::FIELD_TYPE,
        self::FIELD_STATUS,
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_SENT = 'sent';
    public const STATUS_PAID = 'paid';

    /**
     * Types of the source
     * can be uploaded or has some form
     */
    public const TYPE_UPLOAD = 'upload';
    public const TYPE_FORM = 'form';

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Invoice::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_PAYMENT_ID => 0,
            self::FIELD_TYPE => self::TYPE_UPLOAD,
            self::FIELD_STATUS => self::STATUS_NEW,
            self::FIELD_CREATED_BY => 0,
        ];
    }

    public function getStatus(Invoice $invoice): string
    {
        return $invoice->getAttribute(self::FIELD_STATUS);
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    public function isPaid(Invoice $invoice): bool
    {
        return $this->getStatus($invoice) === self::STATUS_PAID;
    }

    public function create(array $data = []): Model
    {
        /** @var Invoice $invoice */
        $invoice = parent::create($data);
        event(new InvoiceChangedEvent($invoice));
        return $invoice;
    }

    public function findAndUpdate(array $filterByFields, array $data): Model
    {
        /** @var Invoice $previous */
        $previous = $this->first(
            $this->convertToFilter($filterByFields, $data)
        );
        /** @var Invoice $invoice */
        $invoice = parent::findAndUpdate($filterByFields, $data);
        event(new InvoiceChangedEvent($invoice, $previous));
        return $invoice;
    }

    /**
     * @param Invoice $invoice
     * @param int $price
     * @param FinanceCurrency $currency
     * @throws InconsistentDataException
     */
    public function setPrice(Invoice $invoice, int $price, FinanceCurrency $currency): void
    {
        /** @var Payment $payment */
        $paymentId = $invoice->getAttribute(self::FIELD_PAYMENT_ID);
        if (!$paymentId) {
            $payment = $this->getPaymentService()->create([
                PaymentService::FIELD_VALUE => $price,
                PaymentService::FIELD_CURRENCY_ID => $currency->getKey(),
                PaymentService::FIELD_FIXED => 0,
            ]);
            $invoice->payment()->associate($payment);
            $invoice->save();
        } else {
            $this->getPaymentService()->findAndUpdate([PaymentService::FIELD_ID], [
                PaymentService::FIELD_ID => $paymentId,
                PaymentService::FIELD_VALUE => $price,
                PaymentService::FIELD_CURRENCY_ID => $currency->getKey(),
            ]);
        }
    }

    private function getPaymentService(): PaymentService
    {
        return $this->getServiceLocator()->get(PaymentService::class);
    }
}
