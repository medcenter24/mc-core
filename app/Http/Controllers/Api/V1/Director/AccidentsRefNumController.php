<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Transformers\AccidentTransformer;

class AccidentsRefNumController extends ApiController
{
    public function search(Request $request): Response
    {
        $query = $this->getQueryString($request);
        $query = '%' . $query . '%';
        $iLike = config('database.default') === 'pgsql' ? 'ILIKE' : 'LIKE';
        $accidents = Accident::where(AccidentService::FIELD_REF_NUM, $iLike, $query)
            ->orWhere(AccidentService::FIELD_ASSISTANT_REF_NUM, $iLike, $query)
            ->paginate(50);
        return $this->response->paginator($accidents, $this->getDataTransformer());
    }

    #[Pure] protected function getDataTransformer(): TransformerAbstract
    {
        return new AccidentTransformer();
    }

    private function getQueryString(Request $request): string
    {
        $query = json_decode($request->getContent(), true)['filter']['fields'][0] ?? [];
        $field = $query['field'] ?? '';
        return $field === 'refNum2' ? $query['value'] : '';
    }
}
