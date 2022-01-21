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

namespace medcenter24\mcCore\App\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use medcenter24\mcCore\App\Http\Controllers\Controller;
use medcenter24\mcCore\App\Services\Core\Logger\DebugLoggerTrait;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use \Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends Controller
{
    use Helpers;
    use ServiceLocatorTrait;
    use DebugLoggerTrait;

    public function __construct()
    {
        parent::__construct();
        Auth::setDefaultDriver('api');
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return Response|null
     */
    public function callAction($method, $parameters): ?Response
    {
        try {
            return parent::callAction($method, $parameters);
        } catch (ModelNotFoundException $e) {
            $this->log($e->getMessage());
            $this->response->error('Model not found', 404);
        }
        return null;
    }
}
