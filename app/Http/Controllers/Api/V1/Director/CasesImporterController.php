<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Services\CaseImporterService;
use App\Transformers\UploadedFileTransformer;
use App\Upload;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class CasesImporterController extends Controller
{
    use Helpers;

    /**
     * @var CaseImporterService
     */
    private $service;

    /**
     * CasesImporterController constructor.
     * @param CaseImporterService $service
     */
    public function __construct(CaseImporterService $service)
    {
        $this->service = $service;
    }

    /**
     * All imported files by this director that should be imported or deleted
     */
    public function files()
    {

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
                $uploadedCase = $this->service->upload($item);
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
        return $this->response->collection($this->service->getUploadedCases($this->user()), new UploadedFileTransformer);
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function import($id)
    {
        $accident = $this->service->import($id);
        return $this->response->accepted('', ['uploadId' => $id, 'accidentId' => $accident->id]);
    }

    public function destroy ($id)
    {
        $this->service->delete($id);
        return $this->response->noContent();
    }
}
