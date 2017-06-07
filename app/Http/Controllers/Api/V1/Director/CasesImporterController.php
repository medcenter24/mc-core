<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Services\CaseImporterService;
use App\Services\UploaderService;
use App\Transformers\UploadedFileTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CasesImporterController extends ApiController
{
    /**
     * @var CaseImporterService
     */
    private $importerService;

    /**
     * @var UploaderService
     */
    private $uploaderService;

    /**
     * CasesImporterController constructor.
     * @param CaseImporterService $importerService
     * @param UploaderService $uploaderService
     */
    public function __construct(CaseImporterService $importerService, UploaderService $uploaderService)
    {
        $this->importerService = $importerService;

        $this->uploaderService = $uploaderService;
        $this->uploaderService->setOptions([
            UploaderService::CONF_DISK => 'imports',
            UploaderService::CONF_FOLDER => 'cases',
        ]);
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function upload(Request $request)
    {
        if (!count($request->allFiles())) {
            $this->response->errorBadRequest('You need to provide files for import');
        }

        $uploadedFiles = new Collection();
        foreach ($request->allFiles() as $file) {
            foreach ($file as $item) {
                $uploadedCase = $this->uploaderService->upload($item);
                $this->user()->uploadedCases()->save($uploadedCase);
                $uploadedFiles->put($uploadedCase->id, $uploadedCase);
            }
        }

        return $this->response->collection($uploadedFiles, new UploadedFileTransformer);
    }

    /**
     * Already loaded list of files
     * @return \Dingo\Api\Http\Response
     */
    public function uploads()
    {
        $uploadedCases = $this->user()->uploadedMedia()->where('storage', $this->uploaderService->getOption(UploaderService::CONF_FOLDER))->get();
        return $this->response->collection($uploadedCases, new UploadedFileTransformer);
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function import($id)
    {
        $path = $this->uploaderService->getPathById($id);
        $accident = $this->importerService->import($path);
        $this->uploaderService->delete($id);

        return $this->response->accepted(
            url('director/cases', [$accident->id]),
            ['uploadId' => $id, 'accidentId' => $accident->id]
        );
    }

    /**
     * Delete uploaded file
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy ($id)
    {
        $this->uploaderService->delete($id);
        return $this->response->noContent();
    }
}
