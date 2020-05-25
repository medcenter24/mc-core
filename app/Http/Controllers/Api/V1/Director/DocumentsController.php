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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorDocumentRequest;
use medcenter24\mcCore\App\Services\Entity\DocumentService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Transformers\DocumentTransformer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentsController extends ApiController
{

    use ServiceLocatorTrait;

    /**
     * @return DocumentService
     */
    private function getDocumentService(): DocumentService
    {
        return $this->getServiceLocator()->get(DocumentService::class);
    }

    private function getRequestedDocument(int $id): Document
    {
        /** @var Document $document */
        $document = $this->getDocumentService()->first([DocumentService::FIELD_ID => $id]);
        if (!$document) {
            $this->response->errorNotFound();
        }
        return $document;
    }

    /**
     * Upload
     * @param int $id
     * @return BinaryFileResponse
     */
    public function show(int $id): BinaryFileResponse
    {
        $document = $this->getRequestedDocument($id);

        if (!$document->hasMedia(DocumentService::CASES_FOLDERS)) {
            $this->response->errorBadRequest('Image does not exist');
        }
        if (!$this->getServiceLocator()->get(DocumentService::class)->checkAccess($document, $this->user())) {
            $this->response->errorForbidden();
        }

        $media = $document->getFirstMedia(DocumentService::CASES_FOLDERS);
        if (!$media) {
            $this->response->errorNotFound();
        }

        return response()->download($media->getPath());
    }

    /**
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function destroy(int $id): Response
    {
        $document = $this->getRequestedDocument($id);

        if (!$this->getServiceLocator()->get(DocumentService::class)->checkAccess($document, $this->user())) {
            $this->response->errorForbidden();
        }

        $document->delete();
        Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }

    /**
     * @param $id
     * @param DoctorDocumentRequest $request
     * @return Response
     */
    public function update(int $id, DoctorDocumentRequest $request): Response
    {
        $document = $this->getRequestedDocument($id);
        try {
            $this->getServiceLocator()->get(DocumentService::class)
                ->changeDocType($document, $request->json('type', DocumentService::TYPE_PASSPORT));
        }catch (CommonException $e) {
            $this->response->errorForbidden();
        }
        return $this->response->item($document, new DocumentTransformer());
    }
}
