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

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use App\TelegramUser;
use Illuminate\Http\Request;

class UsersController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TelegramUser  $telegramUser
     * @return \Illuminate\Http\Response
     */
    public function show(TelegramUser $telegramUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TelegramUser  $telegramUser
     * @return \Illuminate\Http\Response
     */
    public function edit(TelegramUser $telegramUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TelegramUser  $telegramUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TelegramUser $telegramUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TelegramUser  $telegramUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(TelegramUser $telegramUser)
    {
        //
    }
}
