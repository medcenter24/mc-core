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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\DocumentService;
use medcenter24\mcCore\App\Transformers\DocumentTransformer;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class CaseDocumentController extends ApiController
{
    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @param $id
     * @return Response
     */
    public function documents($id): Response
    {
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $documents = $this->getServiceLocator()->get(DocumentService::class)->getDocuments($this->user(), $accident, 'accident');
        return $this->response->collection($documents, new DocumentTransformer());
    }

    /**
     * @param $id
     * @param JsonRequest $request
     * @return Response
     * @throws FileDoesNotExist
     * @throws FileIsTooBig|CommonException
     */
    public function createDocuments($id, JsonRequest $request): Response
    {
        /** @var DocumentService $documentService */
        $documentService = $this->getServiceLocator()->get(DocumentService::class);
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $created = collect([]);
        foreach ($request->allFiles() as $file) {
            $document = $documentService->createDocumentsFromFiles($file, $this->user());
            /** @var Document $doc */
            foreach ($document as $doc) {
                $documentService->changeDocType($doc, DocumentService::TYPE_PASSPORT);
                $accident->documents()->attach($doc);
                $accident->patient?->documents()->attach($doc);
                $created->push($doc);
            }
        }

        return $this->response->collection($created, new DocumentTransformer());
    }
}
