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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


/**
 * Uploader
 * It is more like a Uploader at all (not just a media)
 * to upload medias, please use Media library
 *
 * Class MediaController
 * @package App\Http\Controllers\Api\V1\Director
 */
class MediaController extends ApiController
{
    /**
     * @var UploaderService
     */
    private $service;

    public function __construct(UploaderService $uploaderService)
    {
        $this->service = $uploaderService;
        $this->service->setOptions([
            UploaderService::CONF_DISK => 'local',
            UploaderService::CONF_FOLDER => 'files',
        ]);
    }

    public function upload(Request $request)
    {
        if (!count($request->allFiles())) {
            $this->response->errorBadRequest('You need to provide files for upload');
        }

        $uploadedFiles = new Collection();
        foreach ($request->allFiles() as $file) {
            $uploadedCase = null;
            if ($file instanceof UploadedFile) {
                $uploadedCase = $this->service->upload($file);
            } elseif (count($file)) {
                foreach ($file as $item) {
                    $uploadedCase = $this->service->upload($item);
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
     * @return \Dingo\Api\Http\Response
     */
    public function uploads()
    {
        $uploadedCases = $this->user()->uploads()->where('storage', $this->service->getOption(UploaderService::CONF_FOLDER))->get();
        return $this->response->collection($uploadedCases, new UploadedFileTransformer);
    }

    /**
     * Delete uploaded file
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy ($id)
    {
        $this->service->delete($id);
        return $this->response->noContent();
    }
}
