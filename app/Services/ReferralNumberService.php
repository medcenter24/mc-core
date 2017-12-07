<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;

use App\Accident;
use App\DoctorAccident;
use Carbon\Carbon;

/**
 * Generator of the referral numbers for the accidents
 * Class ReferralNumberService
 * @package App\Services
 */
class ReferralNumberService
{
    const SEPARATOR = '-';

    /**
     * @var AccidentService
     */
    private $accidentService;

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
    public function exists($ref = '')
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
     *     D - Day/Night/Weekend
     *
     * @param Accident $accident
     * @return string
     */
    public function generate(Accident $accident)
    {
        $refNum = $accident->ref_num;
        if (!$refNum) {
            $ref = $this->getAssistantKey($accident);
            $ref .= $this->getNumberKey($accident);
            $ref .= self::SEPARATOR;
            $ref .= Carbon::now()->format('dmy');
            $ref .= self::SEPARATOR;
            $ref .= $this->getCaseableRefKey($accident->caseable);
            $ref .= $this->getTimesOfDayCode(Carbon::now());

            // skip duplicates
            $additionalPrefix = 0;
            while ($this->exists($refNum = $ref
                . ($additionalPrefix ? '^' . ++$additionalPrefix : ''))
            ) { }
        }

        return $refNum;
    }

    private function getCaseableRefKey($caseable = null)
    {
        $ref = 'NA';
        if ($caseable) {
            if ($caseable instanceof DoctorAccident) {
                $ref = $caseable->doctor ? $caseable->doctor->ref_key : 'NA';
            } // hospital accident not implemented yet = elseif ($caseable instanceof )
        }

        return $ref;
    }

    private function getAssistantKey(Accident $accident)
    {
        return $accident->assistant && $accident->assistant->id ?
            $accident->assistant->ref_key
            : 'NA';
    }

    private function getNumberKey(Accident $accident)
    {
        $startDate = Carbon::now()->format('Y') . '-01-01 00:00:00';
        $count = $this->accidentService->getCountByAssistance($accident->assistant_id, $startDate);
        return sprintf('%04d', $count);
    }

    public function getTimesOfDayCode(Carbon $date)
    {
        $mark = 'N'; // night
        if (in_array($date->dayOfWeek, [0,6])) {
            $mark = 'W'; // weekend
        } elseif($date->hour >= 8 && $date->hour <= 21) {
            $mark = 'D'; // day
        }
        return $mark;
    }
}
