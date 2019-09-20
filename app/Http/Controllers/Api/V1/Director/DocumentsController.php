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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Document;
use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorDocumentRequest;
use medcenter24\mcCore\App\Services\DocumentService;
use medcenter24\mcCore\App\Services\ServiceLocatorTrait;
use medcenter24\mcCore\App\Transformers\DocumentTransformer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentsController extends ApiController
{

    use ServiceLocatorTrait;

    /**
     * Upload
     * @param $id
     * @return BinaryFileResponse
     */
    public function show($id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);
        if (!$document->hasMedia(DocumentService::CASES_FOLDERS)) {
            $this->response->errorBadRequest('Image does not exist');
        }
        if (!$this->getServiceLocator()->get(DocumentService::class)->checkAccess($document, $this->user())) {
            $this->response->errorForbidden();
        }
        $media = $document->getFirstMedia(DocumentService::CASES_FOLDERS);
        return response()->download($media->getPath());
    }

    /**
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        $document = Document::findOrFail($id);
        if (!$this->getServiceLocator()->get(DocumentService::class)->checkAccess($document, $this->user())) {
            $this->response->errorForbidden();
        }

        $document->delete();
        \Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }

    /**
     * @param $id
     * @param DoctorDocumentRequest $request
     * @return Response
     */
    public function update($id, DoctorDocumentRequest $request): Response
    {
        $document = Document::findOrFail($id);
        try {
            $this->getServiceLocator()->get(DocumentService::class)
                ->changeDocType($document, $request->json('type', DocumentService::TYPE_PASSPORT));
        }catch (CommonException $e) {
            $this->response->errorForbidden();
        }
        return $this->response->item($document, new DocumentTransformer());
    }
}
