<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;


use App\Http\Controllers\ApiController;
use App\Services\UploaderService;
use App\Transformers\UploadedFileTransformer;
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
