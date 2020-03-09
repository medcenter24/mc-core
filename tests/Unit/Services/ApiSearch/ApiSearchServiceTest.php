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

namespace medcenter24\mcCore\Tests\Unit\Services\ApiSearch;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Services\ApiSearch\ApiSearchService;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\App\Services\Core\Http\DataLoaderRequestBuilder;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocator;
use medcenter24\mcCore\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ApiSearchServiceTest extends TestCase
{
    /**
     * @throws NotImplementedException
     */
    public function testSearch(): void
    {
        /** @var Paginator|MockObject $paginatorServiceMock */
        $paginatorServiceMock = $this->createMock(Paginator::class);
        $paginatorServiceMock->expects(self::once())->method('create')->willReturnSelf();

        /** @var DataLoaderRequestBuilder|MockObject $dataLoaderRequestBuilderMock */
        $dataLoaderRequestBuilderMock = $this->createMock(DataLoaderRequestBuilder::class);

        /** @var Sorter|MockObject $sorterServiceMock */
        $sorterServiceMock = $this->createMock(Sorter::class);
        $sorterServiceMock->expects(self::once())->method('create')->willReturnSelf();

        /** @var Filter|MockObject $filterServiceMock */
        $filterServiceMock = $this->createMock(Filter::class);
        $filterServiceMock->expects(self::once())->method('create')->willReturnSelf();

        /** @var ServiceLocator|MockObject $serviceLocatorMock */
        $serviceLocatorMock = $this->mockServiceLocator([
            Paginator::class => $paginatorServiceMock,
            Sorter::class => $sorterServiceMock,
            Filter::class => $filterServiceMock,
            DataLoaderRequestBuilder::class => $dataLoaderRequestBuilderMock,
        ]);

        /** @var SearchFieldLogic|MockObject $mockedSearchFieldLogic */
        $mockedSearchFieldLogic = $this->createMock(SearchFieldLogic::class);

        /** @var ApiSearchService $service */
        $service = new ApiSearchService();
        $service->setFieldLogic($mockedSearchFieldLogic);
        $service->setServiceLocator($serviceLocatorMock);

        /** @var LengthAwarePaginator|MockObject $paginatorAwareMock */
        $paginatorAwareMock = $this->createMock(LengthAwarePaginator::class);

        /** @var Builder|MockObject $builderMock */
        $builderMock = $this->createMock(Builder::class);
        $builderMock->expects(self::once())->method('paginate')->willReturn($paginatorAwareMock);

        /** @var Model|MockObject $modelMock */
        $modelMock = $this->createMock(Model::class);
        $modelMock->expects(self::once())->method('newQuery')->willReturn($builderMock);

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(3))
            ->method('json')
            ->willReturnCallback(static function ($dataType, $default) {
                return $default;
            });

        $result = $service->search($requestMock, $modelMock);
        $this->assertSame($paginatorAwareMock, $result);
    }
}
