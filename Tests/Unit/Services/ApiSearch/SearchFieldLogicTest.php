<?php

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Unit\Services\ApiSearch;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;
use medcenter24\mcCore\App\Services\Core\Http\Builders\RequestBuilder;
use medcenter24\mcCore\Tests\TestCase;

class SearchFieldLogicTest extends TestCase
{
    private SearchFieldLogic $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SearchFieldLogic();
    }

    public function testGetInternalFieldName(): void
    {
        $this->assertSame(
            'php_unit_test_string',
                    $this->service->getInternalFieldName('php unit test string')
        );
    }

    public function testGetExternalFieldName(): void
    {
        $this->assertSame(
            'phpUnitTestString',
            $this->service->getExternalFieldName('php unit test string')
        );
    }

    public function testTransformFieldToInternalFormat(): void
    {
        $this->assertSame(
            [RequestBuilder::FIELD_NAME => 'php_unit_test_string'],
            $this->service->transformFieldToInternalFormat([
                RequestBuilder::FIELD_NAME => 'php unit test string'
            ])
        );
    }

    public function testTransformFieldsToExternalFormat(): void
    {
        $this->assertSame(
            [[RequestBuilder::FIELD_NAME => 'phpUnitTestString']],
            $this->service->transformFieldsToExternalFormat([[
                RequestBuilder::FIELD_NAME => 'php unit test string'
            ]])
        );
    }

    public function testGetRelationsInit(): void
    {
        $this->assertInstanceOf(Collection::class, $this->service->getRelations());
    }

    public function testGetFiltersInit(): void
    {
        $this->assertInstanceOf(Collection::class, $this->service->getFilters());
    }
}
