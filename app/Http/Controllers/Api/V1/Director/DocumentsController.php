<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Document;
use App\Http\Controllers\ApiController;
use App\Services\DocumentService;

class DocumentsController extends ApiController
{
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
        $document->delete();
        \Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }
}