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

namespace medcenter24\mcCore\Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\Entity\RoleService;

trait TestTraitApi
{
    use RefreshDatabase;
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return $this->headers($this->getLoggedUser());
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return TestResponse
     */
    protected function send(string $method, string $uri, array $data = []): TestResponse
    {
        return $this->json($method, $uri, $data, $this->getHeaders());
    }

    protected function sendGet(string $uri): TestResponse
    {
        return $this->send('GET', $uri);
    }

    protected function sendPost(string $uri, array $data): TestResponse
    {
        return $this->send('POST', $uri, $data);
    }

    protected function sendPatch(string $uri, array $data): TestResponse
    {
        return $this->send('PATCH', $uri, $data);
    }

    protected function sendPut(string $uri, array $data): TestResponse
    {
        return $this->send('PUT', $uri, $data);
    }

    protected function sendDelete(string $uri): TestResponse
    {
        return $this->send('DELETE', $uri);
    }

    protected function sendOptions(string $uri): TestResponse
    {
        return $this->send('OPTIONS', $uri);
    }
}
