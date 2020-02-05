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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Hash;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\UserStore;
use medcenter24\mcCore\App\Http\Requests\Api\UserUpdate;
use medcenter24\mcCore\App\Role;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\RoleService;
use medcenter24\mcCore\App\Transformers\UserTransformer;
use medcenter24\mcCore\App\User;
use League\Fractal\TransformerAbstract;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded;

class UsersController extends ApiController
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    protected function getDataTransformer(): TransformerAbstract
    {
        return new UserTransformer();
    }

    // get only users which assigned to doctors
    public function index(): Response
    {
        $users = Role::where('title', RoleService::DOCTOR_ROLE)->get()->first()->users;
        return $this->response->collection($users, new UserTransformer());
    }

    /**
     * Director has access only for himself or for all doctors
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $user = User::findOrFail($id);
        if ($user->id != $this->user()->id && !\Roles::hasRole($user, RoleService::DOCTOR_ROLE)) {
            \Log::info('Director has no access to the user', [$user]);
            $this->response->errorMethodNotAllowed();
        }
        return $this->response->item($user, new UserTransformer());
    }

    public function update($id, UserUpdate $request): Response
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

    public function store(UserStore $request): Response
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
        $user->roles()->attach(Role::where('title', RoleService::DOCTOR_ROLE)->get()->first());
        $transformer = new UserTransformer();
        return $this->response->created(null, $transformer->transform($user));
    }

    public function updatePhoto($id): Response
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

    public function deletePhoto($id): Response
    {
        $user = User::findOrFail($id);
        $user->clearMediaCollection(LogoService::FOLDER);
        return $this->response->noContent();
    }

}
