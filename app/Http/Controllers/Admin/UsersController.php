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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Http\Requests\UserRequest;
use medcenter24\mcCore\App\Entity\User;

class UsersController extends AdminController
{
    /**
     * @return Factory|View|Application
     * @throws InconsistentDataException
     */
    public function index(): Factory|View|Application
    {
        $this->getMenuService()->markCurrentMenu('1.10');
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $this->getMenuService()->markCurrentMenu('1.10');
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $this->getMenuService()->markCurrentMenu('1.10');
        $user = new User();
        return view('admin.users.create', compact('user'));
    }

    public function edit($id)
    {
        $this->getMenuService()->markCurrentMenu('1.10');
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function store(UserRequest $request)
    {
        $this->validate($request, [
            'password' => 'required',
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

        $this->saveAssignments($user, $request);

        return redirect('admin/users/' . $user->id);
    }

    public function saveAssignments(User $user, UserRequest $request): void
    {
        if ($request->has('password') && !empty($request->input('password'))) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

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
