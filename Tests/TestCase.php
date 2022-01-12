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

namespace medcenter24\mcCore\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocator;
use PHPUnit\Framework\MockObject\MockObject;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private const ERR_CODES = [500, 422, 405];
    private array $errCodes = self::ERR_CODES;

    protected function doNotPrintErrResponse($expectedErrCodes = []): void
    {
        if (count($expectedErrCodes)) {
            $this->errCodes = [];
            foreach (self::ERR_CODES as $errCode) {
                if (!in_array($errCode, $expectedErrCodes, true)) {
                    $this->errCodes[] = $errCode;
                }
            }
        } else {
            $this->errCodes = self::ERR_CODES;
        }
    }

    /**
     * I want to see all 500 errors
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return TestResponse|void
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): ?TestResponse
    {
        $res = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        if (in_array($res->getStatusCode(), $this->errCodes, true)) {
            echo "\n <================ PRINT CONTENT ==============> \n";
            print_r(json_decode($res->getContent(), false));
            echo "\n <================ / END PRINT CONTENT ==============> \n";
        }

        return $res;
    }

    /**
     * @param array $services
     * @return ServiceLocator
     */
    protected function mockServiceLocator(array $services): ServiceLocator
    {
        /** @var ServiceLocator|MockObject $serviceLocator */
        $serviceLocatorMock = $this->createMock(ServiceLocator::class);
        $serviceLocatorMock->expects(self::any())->method('get')
            ->willReturnCallback(function($serviceName) use ($services) {
                return $services[$serviceName] ?? null;
            });


        return $serviceLocatorMock;
    }
}
