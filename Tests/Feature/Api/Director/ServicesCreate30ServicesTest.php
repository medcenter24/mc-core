<?php

declare(strict_types=1);

namespace Api\Director;

use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class ServicesCreate30ServicesTest extends TestCase
{
    use DirectorTestTraitApi;

    private const URI = 'api/director/services';

    public function testCreate(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $data = ['title' => 'title_' . $i];

            $response = $this->post(self::URI, $data, $this->headers($this->getUser()));
            $response->assertStatus(201);
            $response->assertJson([
                'data' =>
                    [
                        'id' => $i + 1,
                        'title' => 'title_' . $i,
                        'description' => '',
                        'status' => 'active',
                        'diseases' => [],
                        'type' => 'director',
                    ],
            ]);
        }

        // request with body content

        $server = $this->transformHeadersToServerVars($this->headers($this->getUser()));
        $cookies = $this->prepareCookiesForRequest();

        $response = $this->call(
            'POST',
            self::URI . '/search',
            [],
            $cookies,
            [],
            $server,
            json_encode(['paginator' => ['fields' => [['field' => 'limit', 'value' => 31]]]]),
            // "['fields' => [{field: 'offset', 'value': 10}, {field: 'limit', value: 25}]]"
        );
        $response->assertStatus(200);
        $response->assertJson($this->getResultArray());
    }

    #[ArrayShape(['data' => "array[]", 'meta' => "array[]"])]
    private function getResultArray(): array
    {
        return array(
            'data' =>
                array(
                    0 =>
                        array(
                            'id' => 1,
                            'title' => 'title_0',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    1 =>
                        array(
                            'id' => 2,
                            'title' => 'title_1',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    2 =>
                        array(
                            'id' => 3,
                            'title' => 'title_2',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    3 =>
                        array(
                            'id' => 4,
                            'title' => 'title_3',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    4 =>
                        array(
                            'id' => 5,
                            'title' => 'title_4',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    5 =>
                        array(
                            'id' => 6,
                            'title' => 'title_5',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    6 =>
                        array(
                            'id' => 7,
                            'title' => 'title_6',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    7 =>
                        array(
                            'id' => 8,
                            'title' => 'title_7',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    8 =>
                        array(
                            'id' => 9,
                            'title' => 'title_8',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    9 =>
                        array(
                            'id' => 10,
                            'title' => 'title_9',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    10 =>
                        array(
                            'id' => 11,
                            'title' => 'title_10',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    11 =>
                        array(
                            'id' => 12,
                            'title' => 'title_11',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    12 =>
                        array(
                            'id' => 13,
                            'title' => 'title_12',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    13 =>
                        array(
                            'id' => 14,
                            'title' => 'title_13',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    14 =>
                        array(
                            'id' => 15,
                            'title' => 'title_14',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    15 =>
                        array(
                            'id' => 16,
                            'title' => 'title_15',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    16 =>
                        array(
                            'id' => 17,
                            'title' => 'title_16',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    17 =>
                        array(
                            'id' => 18,
                            'title' => 'title_17',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    18 =>
                        array(
                            'id' => 19,
                            'title' => 'title_18',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    19 =>
                        array(
                            'id' => 20,
                            'title' => 'title_19',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    20 =>
                        array(
                            'id' => 21,
                            'title' => 'title_20',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    21 =>
                        array(
                            'id' => 22,
                            'title' => 'title_21',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    22 =>
                        array(
                            'id' => 23,
                            'title' => 'title_22',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    23 =>
                        array(
                            'id' => 24,
                            'title' => 'title_23',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    24 =>
                        array(
                            'id' => 25,
                            'title' => 'title_24',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    25 =>
                        array(
                            'id' => 26,
                            'title' => 'title_25',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    26 =>
                        array(
                            'id' => 27,
                            'title' => 'title_26',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    27 =>
                        array(
                            'id' => 28,
                            'title' => 'title_27',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    28 =>
                        array(
                            'id' => 29,
                            'title' => 'title_28',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                    29 =>
                        array(
                            'id' => 30,
                            'title' => 'title_29',
                            'description' => '',
                            'status' => 'active',
                            'diseases' =>
                                array(),
                            'type' => 'director',
                        ),
                ),
            'meta' =>
                array(
                    'pagination' =>
                        array(
                            'total' => 30,
                            'count' => 30,
                            'per_page' => 31,
                            'current_page' => 1,
                            'total_pages' => 1,
                            'links' =>
                                array(),
                        ),
                ),
        );
    }
}
