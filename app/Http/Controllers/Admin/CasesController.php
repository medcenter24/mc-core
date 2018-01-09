<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin;


use App\Accident;
use App\Http\Controllers\AdminController;
use App\Services\CaseServices\CaseHistoryService;
use App\Services\CaseServices\CaseReportService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CasesController extends AdminController
{
    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $cases = [];
        foreach (Accident::where('ref_num', 'like', $query.'%')
            ->orderBy('ref_num')
            ->limit($limit)
            ->offset($offset)
            ->withTrashed()
            ->get(['ref_num']) as $row) {
            $cases[] = $row['ref_num'];
        }

        return response()->json($cases);
    }

    public function report(Request $request, CaseReportService $service)
    {
        $accident = Accident::where('ref_num', $request->input('ref', false))->first();
        if (!$accident) {
            abort(404);
        }

        return $service->generate($accident)->toHtml();
    }

    public function downloadPdf(Request $request, CaseReportService $service)
    {
        $accident = Accident::where('ref_num', $request->input('ref', false))->first();
        if (!$accident) {
            abort(404);
        }

        return response()->download($service->generate($accident)->getPdfPath());
    }

    public function history(Request $request, CaseHistoryService $service)
    {
        /** @var Accident $accident */
        $accident = Accident::where('ref_num', $request->input('ref', false))->first();
        if (!$accident) {
            abort(404);
        }

        return response()->json($service->generate($accident)->toArray());
    }
}
