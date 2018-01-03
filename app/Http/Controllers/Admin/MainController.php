<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;

class MainController extends AdminController
{
    public function index()
    {
        return view('admin.main.index');
    }
}
