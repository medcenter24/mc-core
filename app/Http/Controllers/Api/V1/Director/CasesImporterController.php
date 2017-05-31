<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CasesImporterController extends Controller
{
    use Helpers;

    /**
     * Need to be available (headers will be sent through cors)
     */
    public function options()
    {
        /*return $this->response->withHeaders([
            'Allow' => 'POST, PUT, OPTIONS',
            'Access-Control-Allow-Methods' => 'PUT, POST, OPTIONS',
            'Access-Control-Allow-Origin' => 'http://director.mydoctors24.com:8001',
            'Access-Control-Allow-Headers' => 'X-Custom-Header',
        ]);*/
    }

    public function upload(Request $request)
    {
        $path = Storage::putFile('imports', $request->file('case'));

        return $this->response->created($path, ['files' => ['first' => [
            'name' => $path,
            '' => $path,
        ]]]);
    }

    public function import()
    {

    }
}
