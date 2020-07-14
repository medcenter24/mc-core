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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\CaseServices\AccidentStatusVisor;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;
use medcenter24\mcCore\App\Services\Entity\InvoiceService;

class HospitalInvoiceVisorService extends AbstractVisorService
{
    protected function getStatusMap(): array
    {
        return [
            InvoiceService::FIELD_STATUS => [
                self::STATUS_TITLE => AccidentStatusService::STATUS_PAID,
                self::STATUS_TYPE => AccidentStatusService::TYPE_HOSPITAL,
            ]
        ];
    }

    /**
     * @param string $attributeName
     * @param Model $invoice
     * @param Model|null $previousInvoice
     * @return bool
     */
    protected function isStatusUpdatable(
        string $attributeName,
        Model $invoice,
        Model $previousInvoice = null
    ): bool {
        return $invoice instanceof Invoice
            && parent::isStatusUpdatable($attributeName, $invoice, $previousInvoice)
            && $this->getInvoiceService()->isPaid($invoice);
    }

    private function getInvoiceService(): InvoiceService
    {
        return $this->getServiceLocator()->get(InvoiceService::class);
    }

    /**
     * @param Model|Invoice $model
     * @return Accident|null|Model
     */
    protected function popAccident(Model $model): ?Accident
    {
        /** @var HospitalAccident $hospitalAccident */
        $hospitalAccident = $this->getHospitalAccidentService()
            ->first([HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID => $model->getAttribute(InvoiceService::FIELD_ID)]);
        return $this->getAccidentService()->first([
            AccidentService::FIELD_CASEABLE_TYPE => AccidentService::CASEABLE_TYPE_HOSPITAL,
            AccidentService::FIELD_CASEABLE_ID => $hospitalAccident->getAttribute(HospitalAccidentService::FIELD_ID),
        ]);
    }

    private function getHospitalAccidentService(): HospitalAccidentService
    {
        return $this->getServiceLocator()->get(HospitalAccidentService::class);
    }

    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }
}
