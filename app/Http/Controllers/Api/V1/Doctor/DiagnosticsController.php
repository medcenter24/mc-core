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

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Accident;
use App\Diagnostic;
use App\Http\Controllers\ApiController;
use App\Transformers\DiagnosticTransformer;

class DiagnosticsController extends ApiController
{
    /**
     * To make it easy and more usable we don't need diagnostics which were created by doctor
     * because they don't have all data and director should check all these cases
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $diagnostics = Diagnostic::join('diagnosticables', 'diagnosticables.diagnostic_id', '=', 'diagnostics.id')
            ->where('diagnosticables.diagnosticable_type', '=', Accident::class)
            ->orderBy('diagnostics.title')
            ->get();
        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }
}
