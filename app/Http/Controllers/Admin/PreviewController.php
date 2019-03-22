<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
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
