<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Services\Export\Form1ExportService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends AdminController
{
    public function index()
    {
        return view('admin.main.index');
    }
}
