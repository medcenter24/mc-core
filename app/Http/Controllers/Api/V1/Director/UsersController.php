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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Dingo\Api\Http\Response;
use JetBrains\PhpStorm\Pure;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Http\Requests\Api\UserStore;
use medcenter24\mcCore\App\Http\Requests\Api\UserUpdate;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Services\User\UserDoctorSearchFieldLogic;
use medcenter24\mcCore\App\Support\Facades\Roles;
use medcenter24\mcCore\App\Transformers\UserTransformer;
use medcenter24\mcCore\App\Entity\User;
use League\Fractal\TransformerAbstract;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class UsersController extends ModelApiController
{
    #[Pure] protected function getDataTransformer(): TransformerAbstract
    {
        return new UserTransformer();
    }

    protected function getRequestClass(): string
    {
        return UserStore::class;
    }

    protected function getUpdateRequestClass(): string
    {
        return UserUpdate::class;
    }

    /**
     * @return UserService
     */
    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(UserService::class);
    }

    private function getRoleService(): RoleService
    {
        return $this->getServiceLocator()->get(RoleService::class);
    }

    /**
     * Director has access only for himself or for all doctors
     * @param int $id
     * @return Response
     * @throws NotImplementedException
     */
    public function show(int $id): Response
    {
        $user = $this->getModelService()->first(['users.id' => $id]);
        if (!$user) {
            $this->response->errorNotFound();
        }
        if ($user->id !== $this->user()->id && !Roles::hasRole($user, RoleService::DOCTOR_ROLE)) {
            Log::info('Director has no access to the user', [$user]);
            $this->response->errorMethodNotAllowed();
        }
        return $this->response->item($user, $this->getDataTransformer());
    }

    public function destroy($id): Response
    {
        $user = $this->getModelService()->first(['users.id' => $id]);
        if (!$user) {
            $this->response->errorNotFound();
        }
        if (!Roles::hasRole($user, RoleService::DOCTOR_ROLE)) {
            Log::info('Director has no access to the user', [$user]);
            $this->response->errorMethodNotAllowed();
        }
        return parent::destroy($id);
    }

    /**
     * search by the doctors only!
     * @return SearchFieldLogic|null
     */
    protected function searchFieldLogic(): ?SearchFieldLogic
    {
        return $this->getServiceLocator()->get(UserDoctorSearchFieldLogic::class);
    }

    /**
     * @param JsonRequest $request
     * @return Response
     */
    public function store(JsonRequest $request): Response
    {
        /** @var UserStore $request */
        $request = call_user_func([$this->getRequestClass(), 'createFromBase'], $request);
        $request->validate();

        try {
            $data = $this->getDataTransformer()->inverseTransform($request->all());
            /** @var UserService $userService */
            $userService = $this->getModelService();
            $user = $userService->create($data);

            // director could create only users with doctor role
            $doctorRole = $this->getRoleService()->first(['title' => RoleService::DOCTOR_ROLE]);
            $loginRole = $this->getRoleService()->first(['title' => RoleService::LOGIN_ROLE]);
            if (!$doctorRole || !$loginRole) {
                $this->response->errorInternal('Role doctor or login was not assigned');
            }
            $user->roles()->attach($doctorRole);
            $user->roles()->attach($loginRole);

            return $this->response->created(
                $this->urlToTheSource($user->getAttribute('id')),
                [self::API_DATA_PARAM => $this->getDataTransformer()->transform($user)]
            );
        } catch(InconsistentDataException $e) {
            throw new ValidationHttpException([$e->getMessage()]);
        } catch (NotImplementedException | QueryException $e) {
            Log::error($e->getMessage(), [$e]);
            $this->response->errorInternal();
        }

        return $this->response->noContent();
    }

    public function updatePhoto($id): Response
    {
        /** @var User $user */
        $user = $this->getModelService()->first(['users.id' => $id]);
        if (!$user) {
            $this->response->errorNotFound();
        }
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
        $user = $this->getModelService()->first(['users.id' => $id]);
        if (!$user) {
            $this->response->errorNotFound();
        }
        $user->clearMediaCollection(LogoService::FOLDER);
        return $this->response->noContent();
    }

}
