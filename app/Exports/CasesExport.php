<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Exports;

use App\Accident;
use App\Services\CaseServices\CaseSeekerService;
use App\Transformers\CaseExportTransformer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CasesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            trans('content.npp'),
            trans('content.patient_name'),
            trans('content.assistant'),
            trans('content.assistant_ref_num'),
            trans('content.ref_num'),
            trans('content.date'),
            trans('content.time'),
            trans('content.city'),
            trans('content.caseable_type'),
            trans('content.caseable_title'),
            trans('content.caseable_payment'),
            trans('content.report'),
            trans('content.policy'),
            trans('content.passport'),
            trans('content.passport_checks'),
            trans('content.payment_guaranty'),
            trans('content.paid'),
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Collection
    {
        $caseSeekerService = new CaseSeekerService();
        $accidents = $caseSeekerService->search($this->filters);
        $transformer = new CaseExportTransformer();
        $npp = 0;
        return $accidents->map(function (Accident $accident) use ($transformer, &$npp) {
            $data = $transformer->transform($accident);
            array_unshift($data, ++$npp);
            return $data;
        });
    }
}
