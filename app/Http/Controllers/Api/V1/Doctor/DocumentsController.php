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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Document;
use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorDocumentRequest;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\DocumentService;
use medcenter24\mcCore\App\Transformers\DocumentTransformer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The main difference from the directors controller is that doctor can delete only his documents
 * from the active accidents
 *
 * Class DocumentsController
 * @package medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor
 */
class DocumentsController extends ApiController
{
    use ServiceLocatorTrait;

    private function checkAccess(Document $document): void
    {
        if ($document->getAttribute('created_by') !== $this->user()->id) {
            $this->response->errorForbidden();
        }
    }

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
        $this->checkAccess($document);
        $document->delete();
        \Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }

    /**
     * @param int $id
     * @param DoctorDocumentRequest $request
     * @return Response
     */
    public function update(int $id, DoctorDocumentRequest $request): Response
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
