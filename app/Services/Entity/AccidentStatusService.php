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

use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use Illuminate\Database\Eloquent\Model;

class AccidentStatusService extends AbstractModelService
{
    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_TYPE = 'type';

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
    public const STATUS_IMPORTED = 'imported';
    public const STATUS_REOPENED = 'reopened';

    public const STATUS_HOSPITAL_GUARANTEE = 'hospital_guarantee';
    public const STATUS_HOSPITAL_INVOICE = 'hospital_invoice';
    public const STATUS_ASSISTANT_INVOICE = 'assistant_invoice';
    public const STATUS_ASSISTANT_GUARANTEE = 'assistant_guarantee';

    public const FILLABLE = [self::FIELD_TITLE, self::FIELD_TYPE];
    public const VISIBLE = [self::FIELD_ID, self::FIELD_TITLE, self::FIELD_TYPE];
    public const UPDATABLE = [self::FIELD_TITLE, self::FIELD_TYPE];

    protected function getClassName(): string
    {
        return AccidentStatus::class;
    }

    #[ArrayShape([self::FIELD_TITLE => "string", self::FIELD_TYPE => "string"])]
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_TYPE => '',
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
            throw new InconsistentDataException('Parameters should be provided');
        }

        return AccidentStatus::firstOrFail($params);
    }

    public function getClosedStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_CLOSED,
            self::FIELD_TYPE => self::TYPE_ACCIDENT,
        ]);
    }

    public function getImportedStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_IMPORTED,
            self::FIELD_TYPE => self::TYPE_ACCIDENT,
        ]);
    }

    public function getNewStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_NEW,
            self::FIELD_TYPE => self::TYPE_ACCIDENT,
        ]);
    }

    public function getReopenedStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_REOPENED,
            self::FIELD_TYPE => self::TYPE_ACCIDENT,
        ]);
    }

    public function getDoctorSentStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_SENT,
            self::FIELD_TYPE => self::TYPE_DOCTOR,
        ]);
    }

    public function getDoctorInProgressStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_IN_PROGRESS,
            self::FIELD_TYPE => self::TYPE_DOCTOR,
        ]);
    }

    public function getDoctorAssignedStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_ASSIGNED,
            self::FIELD_TYPE => self::TYPE_DOCTOR,
        ]);
    }

    public function getDoctorRejectedStatus(): AccidentStatus|Model
    {
        return $this->firstOrCreate([
            self::FIELD_TITLE => self::STATUS_REJECT,
            self::FIELD_TYPE => self::TYPE_DOCTOR,
        ]);
    }
}
