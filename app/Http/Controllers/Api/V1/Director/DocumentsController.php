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

use App\Document;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DoctorDocumentRequest;
use App\Services\DocumentService;
use App\Services\RoleService;
use App\Transformers\DocumentTransformer;
use PhpParser\Comment\Doc;

class DocumentsController extends ApiController
{

    private $documentService;
    private $roleService;

    public function __construct(DocumentService $documentService, RoleService $roleService)
    {
        $this->documentService = $documentService;
        $this->roleService = $roleService;
    }

    /**
     * Upload
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show($id)
    {
        $document = Document::findOrFail($id);
        if (!$document->hasMedia(DocumentService::CASES_FOLDERS)) {
            $this->response->errorBadRequest('Image does not exist');
        }
        if (!$this->documentService->checkAccess($document, $this->user(), $this->roleService)) {
            $this->response->errorForbidden();
        }
        $media = $document->getFirstMedia(DocumentService::CASES_FOLDERS);
        return response()->download($media->getPath());
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        if (!$this->documentService->checkAccess($document, $this->user(), $this->roleService)) {
            $this->response->errorForbidden();
        }

        $document->delete();
        \Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }

    public function update($id, DoctorDocumentRequest $request)
    {
        $document = Document::findOrFail($id);
        if (!$this->documentService->checkAccess($document, $this->user(), $this->roleService)) {
            $this->response->errorForbidden();
        }
        $document->type = $request->json('type', DocumentService::TYPE_PASSPORT);
        $document->save();

        \Log::info('Document updated', ['documentId' => $id, $request->json()]);

        return $this->response->item($document, new DocumentTransformer());
    }
}
