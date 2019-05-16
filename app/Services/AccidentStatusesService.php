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

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use Illuminate\Database\Eloquent\Model;

class AccidentStatusesService extends AbstractModelService
{
    public const TYPE_ACCIDENT = 'accident';
    public const TYPE_DOCTOR = 'doctor';
    public const TYPE_HOSPITAL = 'hospital';
    public const TYPE_ASSISTANT = 'assistant';

    public const STATUS_NEW = 'new';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SENT = 'sent';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECT = 'reject';
    public const STATUS_CLOSED = 'closed';

    public const STATUS_HOSPITAL_GUARANTEE = 'hospital_guarantee';
    public const STATUS_HOSPITAL_INVOICE = 'hospital_invoice';
    public const STATUS_ASSISTANT_INVOICE = 'assistant_invoice';
    public const STATUS_ASSISTANT_GUARANTEE = 'assistant_guarantee';

    protected function getClassName(): string
    {
        return AccidentStatus::class;
    }

    protected function getRequiredFields(): array
    {
        return [
            'title' => '',
            'type' => '',
        ];
    }

    /**
     * @param array $params
     * @throws InconsistentDataException
     * @return AccidentStatus
     * @deprecated please use firstOrFail method
     */
    public function firstOrFail(array $params = []): AccidentStatus
    {
        if (!count($params)) {
            throw new InconsistentDataException('Parameters should been provided');
        }

        return AccidentStatus::firstOrFail($params);
    }

    /**
     * @return AccidentStatus
     */
    public function getClosedStatus(): Model
    {
        return $this->firstOrCreate([
            'title' => self::STATUS_CLOSED,
            'type' => self::TYPE_ACCIDENT,
        ]);
    }

    /**
     * @return AccidentStatus
     */
    public function getNewStatus(): Model
    {
        return $this->firstOrCreate([
            'title' => self::STATUS_NEW,
            'type' => self::TYPE_ACCIDENT,
        ]);
    }

    /**
     * @return AccidentStatus
     */
    public function getDoctorSentStatus(): Model
    {
        return $this->firstOrCreate([
            'title' => self::STATUS_SENT,
            'type' => self::TYPE_DOCTOR,
        ]);
    }

    /**
     * @return AccidentStatus
     */
    public function getDoctorInProgressStatus(): Model
    {
        return $this->firstOrCreate([
            'title' => self::STATUS_IN_PROGRESS,
            'type' => self::TYPE_DOCTOR,
        ]);
    }

    /**
     * @return AccidentStatus
     */
    public function getDoctorAssignedStatus(): Model
    {
        return $this->firstOrCreate([
            'title' => self::STATUS_ASSIGNED,
            'type' => self::TYPE_DOCTOR,
        ]);
    }

    /**
     * @return AccidentStatus
     */
    public function getDoctorRejectedStatus(): Model
    {
        return $this->firstOrCreate([
            'title' => self::STATUS_REJECT,
            'type' => self::TYPE_DOCTOR,
        ]);
    }

}
