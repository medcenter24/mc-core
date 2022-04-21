<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace Api\Director\Search;

use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class SearcherTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testSearch(): void
    {
        $response = $this->sendPost('/api/director/search/search', json_decode($this->getPayload(), true));
        $response->assertStatus(200);
        $response->assertExactJson('{}');
    }

    public function getPayload(): string
    {
        return '{
  "id": 19,
  "title": "active",
  "type": "searcher",
  "result": "json",
  "filters": {
    "cities": [],
    "assistants": [
      {
        "id": 1,
        "title": "Assistant1",
        "email": "a1@e.com",
        "comment": "",
        "refKey": "a1"
      },
      {
        "id": 2,
        "title": "Assistant2",
        "email": "a2@e.com",
        "comment": "",
        "refKey": "A2"
      }
    ],
    "doctors": [
      {
        "id": 1,
        "name": "a",
        "description": "c",
        "refKey": "b",
        "userId": "0",
        "medicalBoardNumber": "d"
      }
    ],
    "caseableTypes": [
      "doctor",
      "hospital"
    ],
    "patients": [],
    "handlingTimeRanges": [],
    "accidentStatuses": [],
    "accidentTypes": [],
    "visitTimeRanges": [],
    "doctorServices": [],
    "doctorSurveys": [],
    "doctorDiagnostics": []
  },
  "fields": [
    {
      "id": "patient",
      "title": "Patient",
      "order": "",
      "sort": 1
    },
    {
      "id": "city",
      "title": "City",
      "order": "",
      "sort": 2
    },
    {
      "id": "assist-ref-num",
      "title": "Assistant Ref. Number",
      "order": "",
      "sort": 3
    }
  ]
}';
    }
}
