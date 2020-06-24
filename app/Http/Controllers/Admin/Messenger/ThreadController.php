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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Admin\Messenger;

use Illuminate\Support\Facades\Auth;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Http\Requests\Messenger\CreateMessage;
use medcenter24\mcCore\App\Services\Messenger\ThreadService;
use medcenter24\mcCore\App\Entity\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;

class ThreadController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        view()->share('current_menu', '2.30');
    }

    public function index()
    {
        return response()->json(Thread::all());
    }

    public function create(ThreadService $service)
    {
        return response()->json($service->create());
    }

    public function show($id)
    {
        $thread = Thread::findOrFail($id);

        $userId = Auth::id();
        $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();

        // $thread->markAsRead($userId);

        return view('admin.messenger.thread.show', compact('thread', 'users'));
    }

    public function createMessage($id, CreateMessage $request)
    {
        $thread = Thread::findOrFail($id);
        $userId = $request->input('identifier', 0);
        // check that exists
        User::findOrFail($userId);

        Message::create([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'body' => $request->input('text', ''),
        ]);

        // Add Sender to participants
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'last_read' => new Carbon(),
        ]);

        // Recipients - if I will need to add extra participants?
        /*if (Input::has('recipients')) {
            $thread->addParticipant($input['recipients']);
        }*/

        return redirect()->action('Admin\Messenger\ThreadController@show', ['id' => $thread->id]);
    }

    public function counts()
    {
        return response()->json([
            'threads' => Thread::count(),
            'messages' => Message::count(),
        ]);
    }
}
