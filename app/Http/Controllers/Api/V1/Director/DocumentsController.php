<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Document;
use App\Http\Controllers\ApiController;

class DocumentsController extends ApiController
{
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        \Log::info('Document deleted', [$document, $this->user()]);
        return $this->response->noContent();
    }
}
