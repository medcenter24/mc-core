<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\AdminController;

class PreviewController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        view()->share('current_menu', '2.10');
    }

    public function caseReport()
    {
        return view('admin.preview.case.report');
    }
}
