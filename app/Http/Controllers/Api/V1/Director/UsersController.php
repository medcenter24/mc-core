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
use Hash;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded;
use Illuminate\Http\Request;

class UsersController extends ApiController
{
    /**
     * For director allowed only Doctors
     * @param $eloquent
     * @param $request
     * @return mixed
     */
    protected function applyCondition($eloquent, Request $request = null)
    {
        return $eloquent->whereHas('roles', function ($query) {
            $query->where('title', 'doctor');
        });
    }

    protected function getModelClass()
    {
        return User::class;
    }

    protected function getDataTransformer()
    {
        return new UserTransformer();
    }

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
            \Log::info('Director has no access to the user', [$user]);
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
        $user->timezone = $request->json('timezone', 'UTC');

        // reset password
        $password = $request->json('password', false);
        if ($password) {
            $user->password = Hash::make($password);
        }

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
            'password' => Hash::make($request->json('password')),
            'lang' => $request->json('lang', 'en'),
            'timezone' => $request->json('timezone', 'UTC'),
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
        try {
            $user->addMediaFromRequest('file')
                ->toMediaCollection(LogoService::FOLDER, LogoService::DISC);
        } catch (FileCannotBeAdded $e) {
            if (stripos($e->getMessage(), 'unlink(') === false) {
                $this->response->error($e->getMessage(), 500);
            }
        } catch (\ErrorException $e) {
            if (stripos($e->getMessage(), 'unlink(') === false) {
                $this->response->error($e->getMessage(), 500);
            }
        }

        return $this->response->item($user, new UserTransformer());
    }

    public function deletePhoto($id)
    {
        $user = User::findOrFail($id);
        $user->clearMediaCollection(LogoService::FOLDER);
        return $this->response->noContent();
    }

}
