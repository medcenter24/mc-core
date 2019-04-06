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
