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

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentCheckpoint;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceViewService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\AccidentCheckpointService;

class CaseExportTransformer extends TransformerAbstract
{
    use ServiceLocatorTrait;

    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident): array
    {
        $row[] = $accident->patient ? $accident->patient->name : __('content.undefined');
        $row[] = $accident->assistant ? $accident->assistant->title : __('content.undefined');
        $row[] = $accident->assistant_ref_num;
        $row[] = $accident->ref_num;
        $row[] = $accident->created_at->format(config('date.dateFormat'));
        $row[] = $accident->created_at->format(config('date.timeFormat'));

        $city = $accident->getAttribute('city');
        $row[] = $city ? $city->title : trans('content.undefined');

        if ($accident->caseable instanceof DoctorAccident) {
            // caseable_type
            $row[] = trans('content.doctor');
            // caseable title
            $row[] = $accident->caseable->doctor ? $accident->caseable->doctor->name : trans('content.not_set');
        } elseif ($accident->caseable instanceof HospitalAccident) {
            $row[] = trans('content.hospital');
            $row[] = $accident->caseable->hospital ? $accident->caseable->hospital->title : trans('content.not_set');
        } else {
            $row[] = trans('content.not_set');
            $row[] = trans('content.not_set');
        }

        // caseable payment
        /** @var CaseFinanceViewService $caseFinanceService */
        $caseFinanceService = $this->getServiceLocator()->get(CaseFinanceViewService::class);
        try {
            $viewFinance = $caseFinanceService->get($accident, ['caseable']);
            $row[] = $viewFinance->get(0)->get('view');
        } catch (InconsistentDataException $e) {
        } catch (FormulaException $e) {
        } catch (\Throwable $e) {
            Log::warning('Formula error', [$e]);
            $row[] = trans('content.not_set');
        }

        /** @var AccidentCheckpointService $checkpointService */
        $checkpointService = $this->getServiceLocator()->get(AccidentCheckpointService::class);
        $checkpoints = $checkpointService->search()->map(static function (AccidentCheckpoint $checkpoint) {
            return $checkpoint->getAttribute(AccidentCheckpointService::FIELD_ID);
        })->toArray();

        $accidentCheckpoints = $accident->checkpoints->map(static function (AccidentCheckpoint $checkpoint) {
            return $checkpoint->getAttribute(AccidentCheckpointService::FIELD_ID);
        })->toArray();

        foreach ($checkpoints as $checkpoint) {
            $row[] = in_array($checkpoint, $accidentCheckpoints, true) ? '1' : '0';
        }

        return $row;
    }
}
