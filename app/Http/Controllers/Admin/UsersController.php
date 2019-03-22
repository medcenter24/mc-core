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
use App\Http\Requests\UserRequest;
use App\User;

class UsersController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        view()->share('current_menu', '1.10');
    }

    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }
    
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $user = new User();
        return view('admin.users.create', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function store(UserRequest $request)
    {
        $this->validate($request, [
            'password' => 'required',
            'username' => 'unique:users',
            'email' => 'unique:users',
        ]);

        $user = User::create($request->all());
        $this->saveAssignments($user, $request);

        return redirect('admin/users/' . $user->id);
    }
    
    public function update(UserRequest $request, $id)
    {

        $user = User::findOrFail($id);

        $user->email = $request->input('email');
        $user->name = $request->input('name');

        if (!empty($request->input('name', ''))) {
            $user->password = $request->input('password');
        }

        $this->saveAssignments($user, $request);

        return redirect('admin/users/' . $user->id);
    }

    public function saveAssignments(User $user, UserRequest $request)
    {
        if ($request->has('password') && !empty($request->input('password'))) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
        }

        $user->roles()->detach();
        $user->roles()->attach($request->input('roles'));
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('admin/users')->with(['flash_message' => trans('content.deleted') . ' ' . $user->name]);
    }
}
