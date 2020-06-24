<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Dingo\Api\Http\Response;
use Illuminate\Support\Carbon;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Transformers\MessageTransformer;

/**
 * @todo create CommentService + change to ModelApiController
 * Class CaseCommentController
 * @package medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases
 */
class CaseCommentController extends ApiController
{
    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function comments(int $id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var Thread $thread */
        $thread = Thread::firstOrCreate(['subject' => 'Accident_'.$accident->getAttribute(AccidentService::FIELD_ID)]);
        $userId = $this->user()->getKey();
        // $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();
        $thread->markAsRead($userId);

        return $this->response->collection(
            $thread->messages,
            new MessageTransformer()
        );
    }

    /**
     * @param JsonRequest $request
     * @param int $id
     * @return Response
     * @throws InconsistentDataException
     */
    public function addComment(JsonRequest $request, int $id): Response
    {
        $accident = Accident::findOrFail($id);
        $thread = Thread::firstOrCreate(['subject' => 'Accident_'.$accident->id]);
        $userId = $this->user()->getKey();

        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'body' => $request->json('text', ''),
        ]);

        // Add Sender to participants
        Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'last_read' => new Carbon(),
        ]);

        $transform = new MessageTransformer();
        return $this->response->created(null, $transform->transform($message));
    }
}
