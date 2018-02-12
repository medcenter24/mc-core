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

    public function caseHistory()
    {
        view()->share('current_menu', '2.20');
        return view('admin.preview.case.history');
    }

    /**
     * Dashboard of the messenger
     */
    public function messenger()
    {
        view()->share('current_menu', '2.30');
        return view('admin.preview.messenger');
    }

    public function telegram()
    {
        view()->share('current_menu', '4.10');
        return view('admin.preview.telegram.bot');
    }
}
