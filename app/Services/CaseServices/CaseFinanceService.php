<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;

class CaseFinanceService
{
    /**
     * @param Accident $accident
     * @return int
     */
    public function calculateIncome(Accident $accident)
    {
        return 1;
    }

    /**
     * @param Accident $accident
     * @return int
     */
    public function calculateDoctorPayment(Accident $accident)
    {
        return 1;
    }

    /**
     * @param Accident $accident
     * @return int
     */
    public function calculateAssistantPayment(Accident $accident)
    {
        return 1;
    }
}
