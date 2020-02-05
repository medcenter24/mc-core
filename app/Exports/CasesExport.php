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

namespace medcenter24\mcCore\App\Exports;

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Services\CaseServices\CaseSeekerService;
use medcenter24\mcCore\App\Transformers\CaseExportTransformer;
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
        // todo search should be configured with ApiController::
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
