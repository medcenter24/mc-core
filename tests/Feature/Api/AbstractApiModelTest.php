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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\Tests\TestCase;

abstract class AbstractApiModelTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    private const STATUS_CREATED = 201;
    private const STATUS_DELETED = 204;
    private const STATUS_UPDATED = 202;
    private const STATUS_UNPROCESSABLE_ENTITY = 422;
    private const STATUS_OK = 200;

    /**
     * Uri to the route
     * @return string
     */
    abstract protected function getUri(): string;

    /**
     * Class for the service which manages this model
     * @return string
     */
    abstract protected function getModelServiceClass(): string;

    /**
     * Data provider with incorrect data with expected error messages
     * @example ['data' => [], 'expectedResponse' => []]
     * @return array
     */
    abstract public function failedDataProvider(): array;

    /**
     * Data provider with correct data for creation and update
     * @return array
     */
    abstract public function createDataProvider(): array;

    /**
     * Data provider for updater
     * @return array
     */
    abstract public function updateDataProvider(): array;

    /**
     * Search process
     * @return array
     */
    abstract public function searchDataProvider(): array;

    /**
     * Show item
     * @return array
     */
    abstract public function showDataProvider(): array;

    /**
     * @return array
     */
    abstract public function deleteDataProvider(): array;

    /**
     * @return ModelService
     */
    private function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get($this->getModelServiceClass());
    }

    /**
     * @param array $data
     * @return Model
     */
    private function createModel(array $data = []): Model
    {
        return $this->getModelService()->create($data);
    }

    /**
     * @dataProvider failedDataProvider
     * @param array $data
     * @param array $expectedResponse
     */
    public function testIncorrectInputData(array $data, array $expectedResponse): void
    {
        $this->doNotPrintErrResponse([self::STATUS_UNPROCESSABLE_ENTITY]);
        $response = $this->post($this->getUri(), $data, $this->headers($this->getUser()));
        $this->doNotPrintErrResponse();

        $response->assertStatus(self::STATUS_UNPROCESSABLE_ENTITY);
        // transformed requested data
        $response->assertJson($expectedResponse);
    }

    /**
     * @dataProvider createDataProvider
     * @param array $data
     * @param array $expectedResponse
     */
    public function testCreate(array $data, array $expectedResponse): void
    {
        $response = $this->post($this->getUri(), $data, $this->headers($this->getUser()));
        $response->assertStatus(self::STATUS_CREATED);
        // transformed requested data
        $response->assertJson($expectedResponse);
    }

    /**
     * @dataProvider updateDataProvider
     * @param array $data
     * @param array $update
     * @param array $expectedResponse
     */
    public function testUpdate(array $data, array $update, array $expectedResponse): void
    {
        /** @var Model $model */
        $model = $this->createModel($data);
        $response = $this->put($this->getUri().'/'.$model->getAttribute('id'), $update, $this->headers($this->getUser()));
        $response->assertStatus(self::STATUS_UPDATED);
        $response->assertJson($expectedResponse);
    }

    /**
     * @dataProvider deleteDataProvider
     * @param array $data
     */
    public function testDelete(array $data): void
    {
        /** @var Model $model */
        $model = $this->createModel($data);
        $response = $this->delete($this->getUri().'/'.$model->getAttribute('id'), [], $this->headers($this->getUser()));
        $response->assertStatus(self::STATUS_DELETED);
        $response->assertNoContent();
    }

    /**
     * @dataProvider showDataProvider
     * @param array $data
     * @param array $expectedResponse
     */
    public function testShow(array $data, array $expectedResponse): void
    {
        /** @var Model $model */
        $model = $this->createModel($data);
        $response = $this->get($this->getUri().'/'.$model->getAttribute('id'), $this->headers($this->getUser()));
        $response->assertStatus(self::STATUS_OK);
        $response->assertJson($expectedResponse);
    }

    /**
     * @dataProvider searchDataProvider
     * @param array $modelsData
     * @param array $filters
     * @param array $expectedResponse
     */
    public function testSearch(array $modelsData, array $filters, array $expectedResponse): void
    {
        foreach ($modelsData as $modelData) {
            $this->createModel($modelData);
        }
        $response = $this->post($this->getUri() . '/search', $filters, $this->headers($this->getUser()));
        $response->assertStatus(self::STATUS_OK);
        $response->assertJson($expectedResponse);
    }
}
