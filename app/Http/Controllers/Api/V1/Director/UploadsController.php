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


use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Services\UploaderService;
use medcenter24\mcCore\App\Transformers\UploadedFileTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UploadsController extends ApiController
{
    /**
     * @param Request $request
     * @param UploaderService $service
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request, UploaderService $service)
    {
        if (!count($request->allFiles())) {
            $this->response->errorBadRequest('You need to provide files for upload');
        }

        $uploaded = false;
        foreach ($request->allFiles() as $file) {
            if ($file instanceof UploadedFile) {
                $uploaded = $service->upload($file);
                break;
            }
        }

        if ($uploaded) {
            $this->user()->uploads()->save($uploaded);
        }

        $transformer = new UploadedFileTransformer();
        return $this->response->created('', $transformer->transform($uploaded));
    }

    /**
     * Upload
     * @param $id
     * @param UploaderService $service
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show($id, UploaderService $service)
    {
        return response()->download($service->getPathById($id));
    }
}
