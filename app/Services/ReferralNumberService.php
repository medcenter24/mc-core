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

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use Carbon\Carbon;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\UserService;

/**
 * Generator of the referral numbers for the accidents
 * Class ReferralNumberService
 * @package medcenter24\mcCore\App\Services
 */
class ReferralNumberService
{
    use ServiceLocatorTrait;

    private const SEPARATOR = '-';

    private AccidentService $accidentService;

    /**
     * ReferralNumberService constructor.
     * @param AccidentService $accidentService
     */
    public function __construct(AccidentService $accidentService)
    {
        $this->accidentService = $accidentService;
    }

    /**
     * Check if referral number already exists
     * @param string $ref
     * @return bool
     */
    public function exists(string $ref = ''): bool
    {
        return $this->accidentService->getCountByReferralNum($ref) > 0;
    }

    /**
     * If some ref_key could not be provided will be used "NA" marker
     * G0001-010117-TFF
     * G - Referral prefix of the assistance (Global voyager assistance)
     * 0001 - number in order (from the begin of this year)
     * 010117 - date
     * FAD -
     *     FA - Doctor initials = Doctor referral prefix (Foster Abigail) / Hospital referral prefix (SP - Sant Paolo Hospital)
     *     D - Day/Night/Weekend(F)
     *
     * @param Accident $accident
     * @return string
     */
    public function generate(Accident $accident): string
    {
        $refNum = $accident->getAttribute('ref_num');
        if (!$refNum) {
            $nowDate = Carbon::now($this->getUserTimezone());
            $ref = $this->getAssistantKey($accident);
            $ref .= $this->getNumberKey($accident);
            $ref .= self::SEPARATOR;
            $ref .= $nowDate->format('dmy');
            $ref .= self::SEPARATOR;
            $ref .= $this->getCaseableRefKey($accident->caseable);
            $ref .= $this->getTimesOfDayCode($nowDate);

            // skip duplicates
            $additionalPrefix = 0;
            do {
                $refNum = $ref . ($additionalPrefix ? '_' . $additionalPrefix : '');
                $additionalPrefix++;
            } while ($this->exists($refNum));
        }

        return $refNum;
    }

    /**
     * @return string
     */
    private function getUserTimezone(): string
    {
        return $this->getServiceLocator()->get(UserService::class)->getTimezone();
    }

    /**
     * @param null $caseable
     * @return string
     */
    private function getCaseableRefKey($caseable = null): string
    {
        $ref = 'NA';
        if ($caseable) {
            if ($caseable instanceof DoctorAccident) {
                $ref = $caseable->doctor ? $caseable->doctor->ref_key : 'NA';
            } elseif ($caseable instanceof HospitalAccident) {
                $ref = $caseable->hospital ? $caseable->hospital->ref_key : 'NA';
            }
        }

        return $ref;
    }

    private function getAssistantKey(Accident $accident): string
    {
        return $accident->getAttribute('assistant') && $accident->getAttribute('assistant')->getAttribute('id') ?
            $accident->getAttribute('assistant')->getAttribute('ref_key')
            : 'NA';
    }

    private function getNumberKey(Accident $accident): string
    {
        $startDate = Carbon::now()->format('Y') . '-01-01 00:00:00';
        $count = $this->accidentService->getCountByAssistance($accident->assistant_id, $startDate);
        return sprintf('%04d', $count);
    }

    public function getTimesOfDayCode(Carbon $date): string
    {
        $mark = 'N'; // night
        if (in_array($date->dayOfWeek, [0, 6], true)) {
            $mark = 'F'; // weekend
        } elseif($date->hour >= 8 && $date->hour <= 21) {
            $mark = 'D'; // day
        }
        return $mark;
    }
}
