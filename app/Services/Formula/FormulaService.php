<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Models\Formula\FormulaBuilder;
use App\Models\Formula\FormulaBuilderInterface;

class FormulaService
{

    /**
     * @var FormulaViewService
     */
    private $viewService;

    /**
     * @var FormulaResultService
     */
    private $resultService;

    /**
     * FormulaService constructor.
     * @param FormulaViewService $viewService
     * @param FormulaResultService $resultService
     */
    public function __construct(FormulaViewService $viewService, FormulaResultService $resultService)
    {
        $this->viewService = $viewService;
        $this->resultService = $resultService;
    }

    /**
     * Formula builder
     * @return FormulaBuilderInterface
     */
    public function formula()
    {
        return new FormulaBuilder($this->viewService, $this->resultService);
    }

    /**
     * Calculates result
     * @param FormulaBuilderInterface $formula
     * @return int|float
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function getResult(FormulaBuilderInterface $formula)
    {
        return $this->resultService->calculate($formula);
    }

    /**
     * @param FormulaBuilderInterface $formula
     * @return string
     * @throws \Throwable
     */
    public function getFormulaView(FormulaBuilderInterface $formula)
    {
        return $this->viewService->render($formula);
    }
}
