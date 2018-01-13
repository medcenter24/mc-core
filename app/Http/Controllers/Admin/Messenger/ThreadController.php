<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin\Messenger;


use App\Http\Controllers\AdminController;
use App\Http\Requests\Messenger\CreateMessage;
use App\Services\Messenger\ThreadService;
use App\User;
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

        $userId = \Auth::id();
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
