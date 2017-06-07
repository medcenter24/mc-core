<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Services\UploaderService;
use App\Transformers\UploadedFileTransformer;
use Illuminate\Support\Collection;

class MediaController extends ApiController
{
    /**
     * @var UploaderService
     */
    private $service;

    public function __construct(UploaderService $mediaService)
    {
        $this->service = $mediaService;
    }

    public function upload(\Request $request)
    {
        if (!count($request->allFiles())) {
            $this->response->errorBadRequest('You need to provide files for upload');
        }

        $uploadedFiles = new Collection();
        foreach ($request->allFiles() as $file) {
            foreach ($file as $item) {
                $uploadedCase = $this->service->upload($item);
                $this->user()->uploadedCases()->save($uploadedCase);
                $uploadedFiles->put($uploadedCase->id, $uploadedCase);
            }
        }

        return $this->response->collection($uploadedFiles, new UploadedFileTransformer());
    }
}
