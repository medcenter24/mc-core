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
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;

/**
 * Accident Statuses Supervisor
 * Class AbstractVisorService
 * @package medcenter24\mcCore\App\Services\CaseServices\AccidentStatusVisor
 */
abstract class AbstractVisorService
{
    use ServiceLocatorTrait;

    protected const STATUS_TYPE = 'type';
    protected const STATUS_TITLE = 'status';

    abstract protected function getStatusMap(): array;

    /**
     * @param Model $model
     * @param Model|null $previousModel
     * @throws InconsistentDataException
     */
    public function applyChanges(
        Model $model,
        Model $previousModel = null
    ): void
    {
        $accident = $this->popAccident($model);

        if (!$accident) {
            // can happen on the
            // - new models without assignment to the accident
            // - AccidentInvoice and HospitalInvoice are working in parallel on the invoice change event
            return; // don't do status changing when we don't have an accident
        }

        foreach ($this->getStatusMap() as $fieldName => $status) {
            if ($this->isStatusUpdatable($fieldName, $model, $previousModel)) {
                Log::info('Update status', [$accident, $status]);
                $this->updateStatus($accident, $status);
            }
        }
    }

    protected function isStatusUpdatable(
        string $attributeName,
        Model $model,
        Model $previousModel = null
    ): bool
    {
        // if attribute value was changed
        return $model->getAttribute($attributeName)
            && (
                !$previousModel
                || $previousModel->getAttribute($attributeName)
                    !== $model->getAttribute($attributeName)
            );
    }

    /**
     * @param Accident $accident
     * @param array $statusData
     * @throws InconsistentDataException
     */
    protected function updateStatus(Accident $accident, array $statusData): void
    {
        /** @var AccidentStatus $status */
        $status = $this->getAccidentStatusService()->firstOrCreate([
            AccidentStatusService::FIELD_TITLE => $statusData['status'],
            AccidentStatusService::FIELD_TYPE => $statusData['type'],
        ]);

        $this->getAccidentService()->setStatus($accident, $status);
    }

    /**
     * @param Model $model
     * @return Accident|null
     */
    protected function popAccident(Model $model): ?Accident
    {
        if ($model instanceof Accident) {
            $accident = $model;
        } else {
            // DoctorAccident or HospitalAccident
            $accident = $model->getAttribute('accident');
        }
        return $accident;
    }

    private function getAccidentStatusService(): AccidentStatusService
    {
        return $this->getServiceLocator()->get(AccidentStatusService::class);
    }

    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }
}