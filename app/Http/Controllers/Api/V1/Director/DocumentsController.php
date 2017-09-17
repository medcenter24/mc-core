<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
