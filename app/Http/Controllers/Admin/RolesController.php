<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\RoleRequest;
use App\Role;


class RolesController extends AdminController
{
    public function index()
    {
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
