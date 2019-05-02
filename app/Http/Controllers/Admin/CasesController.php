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

namespace medcenter24\mcCore\App\Http\Controllers\Admin;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Services\CaseServices\CaseHistoryService;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Services\FormService;

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

    public function report(Request $request, FormService $service)
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
