<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserStore;
use App\Http\Requests\Api\UserUpdate;
use App\Role;
use App\Services\LogoService;
use App\Transformers\UserTransformer;
use App\User;

class UsersController extends ApiController
{
    // get only users which assigned to doctors
    public function index()
    {
        $users = Role::where('title', Role::ROLE_DOCTOR)->get()->first()->users;
        return $this->response->collection($users, new UserTransformer());
    }

    /**
     * Director has access only for himself or for all doctors
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        if ($user->id != $this->user()->id && !\Roles::hasRole($user, Role::ROLE_DOCTOR)) {
            $this->response->errorMethodNotAllowed();
        }
        return $this->response->item($user, new UserTransformer());
    }

    public function update($id, UserUpdate $request)
    {
        $user = User::findOrFail($id);
        $user->name = $request->json('name', '');
        $user->email = $request->json('email', '');
        $user->phone = $request->json('phone', '');
        $user->lang = $request->json('lang', 'en');
        $user->save();

        \Log::info('User updated', [$user]);

        return $this->response->item($user, new UserTransformer());
    }

    public function store(UserStore $request)
    {
        $user = User::create([
            'name' => $request->json('name', ''),
            'email' => $request->json('email', ''),
            'phone' => $request->json('phone', ''),
            'password' => \Hash::make($request->json('password')),
            'lang' => $request->json('lang', 'en'),
        ]);
        // director could create only users with doctor role
        $user->roles()->attach(Role::where('title', Role::ROLE_DOCTOR)->get()->first());
        $transformer = new UserTransformer();
        return $this->response->created(null, $transformer->transform($user));
    }

    public function updatePhoto($id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->clearMediaCollection(LogoService::FOLDER);
        $user->addMediaFromRequest('file')
            ->toMediaCollection(LogoService::FOLDER, LogoService::DISC);
        return $this->response->created(asset($user->getFirstMediaUrl(LogoService::FOLDER, 'thumb_200')),
            [
                'thumb200' => asset($user->getFirstMediaUrl(LogoService::FOLDER, 'thumb_200')),
                'thumb45' => asset($user->getFirstMediaUrl(LogoService::FOLDER, 'thumb_45')),
                'original' => asset($user->getFirstMediaUrl(LogoService::FOLDER)),
            ]);
    }

    public function deletePhoto($id)
    {
        $user = User::findOrFail($id);
        $user->clearMediaCollection(LogoService::FOLDER);
        return $this->response->noContent();
    }

}
