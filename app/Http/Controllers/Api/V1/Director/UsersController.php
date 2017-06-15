<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Doctor;
use App\Http\Controllers\ApiController;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class UsersController extends ApiController
{
    // get only users which assigned to doctors
    public function index()
    {
        $users = new Collection;
        foreach (Doctor::all() as $doc) {
            if ($doc->user) {
                $users->push($doc->user);
            }
        }

        return $this->response->collection($users, new UserTransformer());
    }

    /**
     * Director has access only for himself or for all doctors
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function get($id)
    {
        $user = User::findOrFail($id);
        if ($user->id != $this->user()->id && !$user->doctor && !$user->doctor->id) {
            $this->response->errorMethodNotAllowed();
        }
        return $this->response->item($user, new UserTransformer());
    }

    public function update($id, Request $request)
    {

    }
}
