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

namespace App\Http\Controllers\Api\V1\Director;

use App\Assistant;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AssistantRequest;
use App\Transformers\AssistantTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class AssistantsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new AssistantTransformer();
    }

    protected function getModelClass(): string
    {
        return Assistant::class;
    }

    public function index(): Response
    {
        $assistants = Assistant::orderBy('title')->get();
        return $this->response->collection($assistants, new AssistantTransformer());
    }

    public function show($id): Response
    {
        $assistant = Assistant::findOrFail($id);
        return $this->response->item($assistant, new AssistantTransformer());
    }

    public function store(AssistantRequest $request): Response
    {
        $assistant = Assistant::create([
            'title' => $request->json('title', ''),
            'ref_key' => $request->json('refKey', ''),
            'email' => $request->json('email', ''),
            'comment' => $request->json('commentary', ''),
        ]);
        $transformer = new AssistantTransformer();
        return $this->response->created(null, $transformer->transform($assistant));
    }

    public function update($id, AssistantRequest $request): Response
    {
        $assistant = Assistant::findOrFail($id);
        $assistant->title = $request->json('title', '');
        $assistant->ref_key = $request->json('refKey', '');
        $assistant->email = $request->json('email', '');
        $assistant->comment = $request->json('commentary', '');
        $assistant->save();
        \Log::info('Assistant updated', [$assistant, $this->user()]);
        return $this->response->item($assistant, new AssistantTransformer());
    }

    public function destroy($id): Response
    {
        $assistant = Assistant::findOrFail($id);
        \Log::info('Assistant deleted', [$assistant, $this->user()]);
        $assistant->delete();
        return $this->response->noContent();
    }
}
