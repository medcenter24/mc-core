<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance\Cases;


use App\Assistant;
use App\City;
use App\Doctor;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaService;
use App\Services\Formula\FormulaViewService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentFake;

class GenerateFinanceConditionTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @var CaseFinanceService
     */
    private $caseFinanceService;

    /**
     * @var FormulaService
     */
    private $formulaService;


    public function setUp()
    {
        parent::setUp();

        $this->formulaService = new FormulaService(
            new FormulaViewService(),
            new FormulaResultService()
        );

        $this->caseFinanceService = new CaseFinanceService($this->formulaService);
    }

    /**
     * Set for doctor profit for all cases
     * one value as a reward
     */
    public function testSimpleDoctorProfit()
    {
        $formula = $this->formulaService->formula()->addInteger(7);

        $this->caseFinanceService
            ->createCondition()
            ->if(Doctor::class, 1)
            ->thenFormula($formula);

        $params = [
            'ref_num' => '',
            'created_by' => 1,
            'patient_id' => 1,
            'accident_type_id' => 1,
            'accident_status_id' => 1,
            'assistant_id' => 1,
            'caseable_id' => 1,
            'form_report_id' => 1,
            'city_id' => 1,
        ];
        $additionalParams = [
            'assistant' => ['ref_key' => 'T', 'id'=>1],
            'doctorAccident' => [],
            'doctor' => [
                'ref_key' => 'DOC',
                'city_id' => 1,
            ],
        ];
        self::assertEquals(7, $this->caseFinanceService->calculateDoctorPayment(AccidentFake::make($params, $additionalParams)));
    }

    /**
     * Set for assistant profit for all cases
     */
    public function testAssistantProfit()
    {
        $formula = $this->formulaService->formula()->addInteger(7);

        $this->caseFinanceService
            ->factory()
            ->if(Assistant::class, 1)
            ->thenAssistantPaymentFormula($formula);
    }

    public function testConditionalDoctorsProfit()
    {
        $condition = $this->caseFinanceService
            ->factory()
            ->if(Doctor::class, 1);

        $formula = $this->formulaService->formula()->addInteger(20);
        $condition2 = $condition->clone();
        $condition2->if(CaseFinanceService::DAY)
            ->thenDoctorPaymentFormula($formula);

        $formula = $this->formulaService->formula()->addInteger(50);
        $condition3 = $condition->clone();
        $condition3->if(CaseFinanceService::NIGHT)
            ->thenDoctorPaymentFormula($formula);

        $formula = $this->formulaService->formula()->addInteger(70);
        $condition4 = $condition->clone();
        $condition4->if(CaseFinanceService::WEEKEND)
            ->thenDoctorPaymentFormula($formula);

        // adding conditions by different cities
        $formula = $this->formulaService->formula()->addInteger(100);
        $condition5 = $condition->clone();
        $condition5->if(CaseFinanceService::WEEKEND)
            ->if(City::class, 1)
            ->thenDoctorPaymentFormula($formula);

        $formula = $this->formulaService->formula()->addInteger(11);
        $condition6 = $condition->clone();
        $condition6->if(CaseFinanceService::WEEKEND)
            ->if(City::class, 2)
            ->thenDoctorPaymentFormula($formula);

        $formula = $this->formulaService->formula()->addInteger(9);
        $condition7 = $condition->clone();
        $condition7->if(CaseFinanceService::WEEKEND)
            ->if(City::class, 2)
            ->if(Assistant::class, 1)
            ->thenDoctorPaymentFormula($formula);
    }

    public function testConditionalAssistantProfit()
    {
        // todo
    }
}
