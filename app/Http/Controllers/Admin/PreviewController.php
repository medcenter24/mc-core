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

namespace medcenter24\mcCore\App\Http\Controllers\Admin;


use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\AdminController;

class PreviewController extends AdminController
{
    /**
     * @return Factory|View
     * @throws InconsistentDataException
     */
    public function caseReport()
    {
        $this->getMenuService()->markCurrentMenu('2.10');
        return view('admin.preview.case.report');
    }

    /**
     * @return Factory|View
     * @throws InconsistentDataException
     */
    public function caseHistory()
    {
        $this->getMenuService()->markCurrentMenu('2.20');
        return view('admin.preview.case.history');
    }

    /**
     * Dashboard of the messenger
     * @return Factory|View
     * @throws InconsistentDataException
     */
    public function messenger()
    {
        $this->getMenuService()->markCurrentMenu('2.30');
        return view('admin.preview.messenger');
    }

    /**
     * @return Factory|View
     * @throws InconsistentDataException
     */
    public function telegram()
    {
        $this->getMenuService()->markCurrentMenu('4.10');
        return view('admin.preview.telegram.bot');
    }

    /**
     * @return View
     * @throws InconsistentDataException
     */
    public function slack(): View
    {
        $this->getMenuService()->markCurrentMenu('4.20');
        return view('admin.preview.slack.log');
    }
}
