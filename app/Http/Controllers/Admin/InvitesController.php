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
use App\Invite;
use App\Services\InviteService;
use App\User;
use Illuminate\Http\Request;

class InvitesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        view()->share('current_menu', '1.30');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invites = Invite::orderBy('valid_to', 'desc')->get();
        $users = User::all();
        return view('admin.invite.index', compact('invites', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param InviteService $service
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \ErrorException
     */
    public function store(Request $request, InviteService $service)
    {
        $user = User::findOrFail($request->input('user', 0));
        $invite = $service->generate($user);
        return redirect('admin/invites')->with(['flash_message' => trans('content.created') . ' ' . $invite->token]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Invite $invite
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Invite $invite)
    {
        $invite->delete();
        return redirect('admin/invites')->with(['flash_message' => trans('content.deleted')]);
    }
}
