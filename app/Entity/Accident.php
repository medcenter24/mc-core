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

namespace medcenter24\mcCore\App\Entity;

use medcenter24\mcCore\App\Services\Entity\AccidentService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Case|Accident|...
 *
 * Class Accident
 * @package App
 */
class Accident extends AccidentAbstract
{
    protected $fillable = AccidentService::FILLABLE;
    protected $dates = AccidentService::DATE_FIELDS;
    protected $visible = AccidentService::VISIBLE;

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, AccidentService::FIELD_CREATED_BY);
    }

    /**
     * Payment from the assistant
     * @return BelongsTo
     */
    public function paymentFromAssistant(): BelongsTo
    {
        return $this->belongsTo(Payment::class, AccidentService::FIELD_ASSISTANT_PAYMENT_ID);
    }

    /**
     * Payment either to doctor or hospital
     * @return BelongsTo
     */
    public function paymentToCaseable(): BelongsTo
    {
        return $this->belongsTo(Payment::class, AccidentService::FIELD_CASEABLE_PAYMENT_ID);
    }

    /**
     * Calculated income
     * @return BelongsTo
     */
    public function incomePayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, AccidentService::FIELD_INCOME_PAYMENT_ID);
    }

    /**
     * Case checkpoints - statuses which could be selected in different order
     *
     * @return BelongsToMany
     */
    public function checkpoints(): BelongsToMany
    {
        return $this->belongsToMany(AccidentCheckpoint::class);
    }

    /**
     * Status changes
     *
     * @return MorphMany
     */
    public function history(): MorphMany
    {
        return $this->morphMany(AccidentStatusHistory::class, 'historyable');
    }

    /**
     * Assistant company
     * @return BelongsTo
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(Assistant::class);
    }

    /**
     * Patient from the accident
     * @return BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AccidentType::class, AccidentService::FIELD_ACCIDENT_TYPE_ID);
    }

    /**
     * Accident report stored as a FormReport element (which use Assignment form template)
     */
    public function formReport(): BelongsTo
    {
        return $this->belongsTo(FormReport::class);
    }

    /**
     * @return MorphTo
     */
    public function caseable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function accidentStatus(): BelongsTo
    {
        return $this->belongsTo(AccidentStatus::class);
    }

    /**
     * @return BelongsTo
     */
    public function assistantInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return BelongsTo
     */
    public function assistantGuarantee(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function isDoctorCaseable(): bool
    {
        return $this->getAttribute(AccidentService::FIELD_CASEABLE_TYPE) === DoctorAccident::class;
    }

    public function isHospitalCaseable(): bool
    {
        return $this->getAttribute(AccidentService::FIELD_CASEABLE_TYPE) === HospitalAccident::class;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, AccidentService::FIELD_PARENT_ID);
    }
}
