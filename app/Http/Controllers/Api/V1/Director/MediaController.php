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
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\UploaderService;
use medcenter24\mcCore\App\Transformers\UploadedFileTransformer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

/**
 * Uploader
 * It is more like a Uploader at all (not just a media)
 * to upload medias, please use Media library
 *
 * Class MediaController
 * @package medcenter24\mcCore\App\Http\Controllers\Api\V1\Director
 */
class MediaController extends ApiController
{
    /**
     * @var UploaderService
     */
    private $service;

    private function getUploaderService(): UploaderService
    {
        if (!$this->service) {
            /** @var UploaderService service */
            $this->service = $this->getServiceLocator()->get(UploaderService::class);
            $this->service->setOptions([
                UploaderService::CONF_DISK => 'local',
                UploaderService::CONF_FOLDER => 'files',
            ]);
        }
        return $this->service;
    }

    public function upload(Request $request): Response
    {
        if (!count($request->allFiles())) {
            $this->response->errorBadRequest('You need to provide files for upload');
        }

        $uploadedFiles = new Collection();
        foreach ($request->allFiles() as $file) {
            $uploadedCase = null;
            if ($file instanceof UploadedFile) {
                $uploadedCase = $this->getUploaderService()->upload($file);
            } elseif (count($file)) {
                foreach ($file as $item) {
                    $uploadedCase = $this->getUploaderService()->upload($item);
                }
            }

            if ($uploadedCase) {
                $this->user()->uploads()->save($uploadedCase);
                $uploadedFiles->put($uploadedCase->id, $uploadedCase);
            }
        }

        return $this->response->collection($uploadedFiles, new UploadedFileTransformer());
    }

    /**
     * Already loaded list of files
     * @return Response
     */
    public function uploads(): Response
    {
        $uploadedCases = $this->user()
            ->uploads()
            ->where('storage', $this->getUploaderService()->getOption(UploaderService::CONF_FOLDER))
            ->get();
        return $this->response->collection($uploadedCases, new UploadedFileTransformer());
    }

    /**
     * Delete uploaded file
     * @param $id
     * @return Response
     */
    public function destroy ($id): Response
    {
        $this->getUploaderService()->delete($id);
        return $this->response->noContent();
    }
}
