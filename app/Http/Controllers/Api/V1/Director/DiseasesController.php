<?php

/**
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
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\Disease;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DiseaseRequest;
use medcenter24\mcCore\App\Transformers\DiseaseTransformer;

class DiseasesController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new DiseaseTransformer();
    }

    protected function getModelClass(): string
    {
        return Disease::class;
    }

    public function update($id, DiseaseRequest $request): Response
    {
        /** @var Disease $disease */
        $disease = Disease::find($id);
        if (!$disease) {
            $this->response->errorNotFound();
        }

        $disease->setAttribute('title', $request->json('title', ''));
        $disease->setAttribute('code', $request->json('code', ''));
        $disease->setAttribute('description', $request->json('description', ''));
        $disease->save();

        $transformer = new DiseaseTransformer();
        return $this->response->accepted(null, $transformer->transform($disease));
    }

    public function store(DiseaseRequest $request): Response
    {
        $disease = Disease::create([
            'title' => $request->json('title', ''),
            'code' => $request->json('code', ''),
            'description' => $request->json('description', ''),
        ]);
        $transformer = new DiseaseTransformer();
        return $this->response->created(null, $transformer->transform($disease));
    }

    public function destroy($id): Response
    {
        $disease = Disease::find($id);
        if (!$disease) {
            $this->response->errorNotFound();
        }
        $disease->delete();
        return $this->response->noContent();
    }
}
