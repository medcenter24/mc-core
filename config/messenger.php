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

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use medcenter24\mcCore\App\Entity\User;
use Cmgmyr\Messenger\Models\Thread;

return [

    'user_model' => User::class,

    'message_model' => Message::class,

    'participant_model' => Participant::class,

    'thread_model' => Thread::class,

    /**
     * Define custom database table names - without prefixes.
     */
    'messages_table' => 'messenger_messages',

    'participants_table' => 'messenger_participants',

    'threads_table' => 'messenger_threads',
];
