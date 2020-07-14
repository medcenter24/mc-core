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

namespace medcenter24\mcCore\App\Listeners\Accident\UpdateAccidentStatus;

use medcenter24\mcCore\App\Events\InvoiceChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\CaseServices\AccidentStatusVisor\AccidentInvoiceVisorService;
use medcenter24\mcCore\App\Services\CaseServices\AccidentStatusVisor\HospitalInvoiceVisorService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class OnInvoiceUpdated
{
    use ServiceLocatorTrait;

    /**
     * @param InvoiceChangedEvent $event
     * @throws InconsistentDataException
     */
    public function handle(InvoiceChangedEvent $event): void
    {
        // if invoice assigned to accident directly - it means that this is AccidentInvoice
        $this->getAccidentInvoiceVisorService()->applyChanges($event->getInvoice(), $event->getPreviousInvoice());
        // if invoice assigned to HospitalAccident directly - that is HospitalInvoice
        $this->getHospitalInvoiceVisorService()->applyChanges($event->getInvoice(), $event->getPreviousInvoice());
    }
    
    public function getAccidentInvoiceVisorService(): AccidentInvoiceVisorService
    {
        return $this->getServiceLocator()->get(AccidentInvoiceVisorService::class);
    }

    public function getHospitalInvoiceVisorService(): HospitalInvoiceVisorService
    {
        return $this->getServiceLocator()->get(HospitalInvoiceVisorService::class);
    }
}
