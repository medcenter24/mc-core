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

use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Http\Requests\RoleRequest;
use medcenter24\mcCore\App\Role;


class RolesController extends AdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     */
    public function index()
    {
        $this->getMenuService()->markCurrentMenu('1.20');
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }
    
    public function store(RoleRequest $request)
    {
        $role = Role::create($request->all());
        return redirect('admin/roles')
            ->with(['flash_message' => trans('content.added') . ': ' . $role->title]);
    }
}
