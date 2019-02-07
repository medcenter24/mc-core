<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices\Finance;


use App\Accident;
use App\Exceptions\InconsistentDataException;
use App\Services\CurrencyService;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaViewService;
use Illuminate\Support\Collection;

class CaseFinanceViewService
{
    public const FINANCE_TYPES = ['income', 'assistant', 'caseable'];

    /**
     * @var CaseFinanceService
     */
    private $caseFinanceService;

    /**
     * @var FormulaResultService
     */
    private $formulaResultService;

    /**
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * @var FormulaViewService
     */
    private $formulaViewService;

    public function __construct(
        CaseFinanceService $caseFinanceService,
        FormulaResultService $formulaResultService,
        CurrencyService $currencyService,
        FormulaViewService $formulaViewService
    ) {
        $this->caseFinanceService = $caseFinanceService;
        $this->formulaResultService = $formulaResultService;
        $this->currencyService = $currencyService;
        $this->formulaViewService = $formulaViewService;
    }

    /**
     * @param Accident $accident
     * @param array $types
     * @return Collection
     * @throws InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function getAccidentFinance(Accident $accident, array $types): Collection
    {
        $financeDataCollection = collect([]);

        $types = array_intersect($types, self::FINANCE_TYPES);
        foreach ($types as $type) {
            $formula = $this->caseFinanceService->newFormula();
            switch ($type) {
                case 'income':
                    if ($accident->getIncomePayment && $accident->getIncomePayment->fixed) {
                        $formula->addFloat($accident->getIncomePayment->value);
                        $fixed = true;
                    } else {
                        // I need to sub 2 different results instead of sub formula builders
                        // to not get 1. big formula 2. data inconsistencies
                        $formula
                            ->subFloat($this->formulaResultService->calculate($this->caseFinanceService->getFromAssistantFormula($accident)))
                            ->subFloat($this->formulaResultService->calculate($this->caseFinanceService->getToCaseableFormula($accident)));
                        $fixed = false;
                    }
                    break;
                case 'assistant':
                    $formula = $this->caseFinanceService->getFromAssistantFormula($accident);
                    $fixed = (bool)$accident->paymentFromAssistant && $accident->paymentFromAssistant->fixed;
                    break;
                case 'caseable':
                    // answer: I need this formula just to use it for consistency
                    // so in the formula I just need to set price from the invoice
                    $formula = $this->caseFinanceService->getToCaseableFormula($accident);
                    $fixed = $accident->paymentToCaseable && $accident->paymentToCaseable->fixed;
                    break;
                default:
                    throw new InconsistentDataException('Undefined finance type');
            }

            if (!$formula->hasConditions()) {
                $formula->addFloat(0);
            }

            // to show full formula, not only the part of it
            $formula = $formula->getBaseFormula();

            $typeResult = collect([
                'type' => $type,
                'loading' => false,
                'value' => $this->formulaResultService->calculate($formula),
                'currency' => $this->currencyService->getDefaultCurrency(),
                'formula' => $this->formulaViewService->render($formula),
                'fixed' => $fixed,
            ]);
            $financeDataCollection->push($typeResult);
        }

        return $financeDataCollection;
    }
}
