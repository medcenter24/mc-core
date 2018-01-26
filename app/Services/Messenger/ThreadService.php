<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Messenger;


use Cmgmyr\Messenger\Models\Thread;

class ThreadService
{
    public function create()
    {
        $thread = Thread::create([
            'subject' => $this->createUniqueSubject(),
        ]);

        return $thread;
    }

    protected function createUniqueSubject()
    {
        $prefix = 'Thread';
        $i = 0;
        do {
            $subject = $prefix . ++$i;
        } while (Thread::getBySubject($subject)->count());

        return $subject;
    }
}
