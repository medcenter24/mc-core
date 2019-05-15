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

namespace medcenter24\mcCore\App;

use medcenter24\mcCore\App\Services\AccidentService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use medcenter24\mcCore\App\Services\ServiceLocator;

/**
 * Case|Accident|...
 *
 * Class Accident
 * @package App
 */
class Accident extends AccidentAbstract
{
    protected $fillable = [
        'parent_id',
        'patient_id',
        'accident_type_id',
        'accident_status_id',
        'assistant_id',
        'assistant_ref_num',
        'assistant_invoice_id',
        'assistant_guarantee_id',
        'form_report_id',
        'city_id',
        'caseable_payment_id',
        'income_payment_id',
        'assistant_payment_id',
        'caseable_id',
        'caseable_type',
        'ref_num',
        'title',
        'address',
        'handling_time',
        'contacts',
        'symptoms',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'handling_time',
    ];

    protected $visible = [
        'id',
        'parent_id',
        'patient_id',
        'accident_type_id',
        'accident_status_id',
        'assistant_id',
        'assistant_ref_num',
        'assistant_invoice_id',
        'assistant_guarantee_id',
        'ref_num',
        'title',
        'city_id',
        'address',
        'contacts',
        'symptoms',
        'handling_time',
        'form_report_id',
    ];

    /**
     * On the save action we need to change status (if it is not status changing action only)
     * @var bool
     */
    private $statusUpdating = false;

    public static function boot(): void
    {
        parent::boot();

        self::saved(static function(Accident $accident) {
            $serviceLocator = ServiceLocator::instance();
            $serviceLocator->get(AccidentService::class)->updateAccidentStatus($accident);
        });
    }

    public function isStatusUpdatingRun(): bool
    {
        return $this->statusUpdating;
    }

    public function runStatusUpdating(): void
    {
        $this->statusUpdating = true;
    }

    public function stopStatusUpdating(): void
    {
        $this->statusUpdating = false;
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Payment either to doctor or hospital
     * @return BelongsTo
     */
    public function paymentToCaseable(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'caseable_payment_id');
    }

    /**
     * Calculated income
     * @return BelongsTo
     */
    public function incomePayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'income_payment_id');
    }

    /**
     * Payment from the assistant
     * @return BelongsTo
     */
    public function paymentFromAssistant(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'assistant_payment_id');
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
        return $this->belongsTo(AccidentType::class, 'accident_type_id');
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
        return $this->getAttribute('caseable_type') === DoctorAccident::class;
    }

    public function isHospitalCaseable(): bool
    {
        return $this->getAttribute('caseable_type') === HospitalAccident::class;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
