<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Document;
use App\Http\Controllers\ApiController;
use App\Services\DocumentService;

/**
 * The main difference from the directors controller is that doctor can delete only his documents
 * from the active accidents
 *
 * Class DocumentsController
 * @package App\Http\Controllers\Api\V1\Doctor
 */
class DocumentsController extends ApiController
{
    private function checkAccess(Document $document)
    {
        if ($document->created_by != $this->user()->id) {
            $this->response->errorForbidden();
        }
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
        $media = $document->getFirstMedia(DocumentService::CASES_FOLDERS);
        return response()->download($media->getPath());
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $this->checkAccess($document);
        $document->delete();
        \Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }
}
