<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;


use App\Http\Controllers\ApiController;
use App\Services\UploaderService;
use App\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


class FroalaController extends ApiController
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

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function upload(Request $request)
    {
        if (!count($request->allFiles())) {
            $this->response->errorBadRequest('You need to provide files for upload');
        }

        $uploadedFiles = new Collection();
        $data = [];
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
                $data[] = [
                    'id'   => $uploadedCase->id,
                    'url' => url('director/froala/' . $uploadedCase->id),
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Access to the image only with token
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $media = Upload::findOrFail($id);
        return Image::make($media->getPath())->response();
    }

    /**
     * Already loaded list of files
     * @return \Dingo\Api\Http\Response
     */
    public function uploads()
    {
        $uploads = $this->user()->uploads()->where('storage', $this->service->getOption(UploaderService::CONF_FOLDER))->get();
        $data = [];
        foreach ($uploads as $file) {
            $data[] = [
                'id'   => $file->id,
                'url' => url('director/froala/' . $file->id),
            ];
        }
        return response()->json($data);
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
