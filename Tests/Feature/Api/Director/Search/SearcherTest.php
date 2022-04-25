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
        $response->assertExactJson([]);
    }

    public function getPayload(): string
    {
        return '{
  "id": 20,
  "title": "2022-04-22 00:08:22",
  "type": "searcher",
  "result": "json",
  "filters": {
    "cities": [
      {
        "id": 1,
        "title": "City",
        "regionId": 1,
        "regionTitle": "Region",
        "countryTitle": "Country"
      }
    ],
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
    "patients": [
      {
        "id": 1,
        "name": "TEST-TESTOV",
        "address": "asdf",
        "phones": "123",
        "birthday": "1212-12-12",
        "comment": "asdf"
      }
    ],
    "handlingTimeRanges": [
      "2022-04-19>2022-04-24"
    ],
    "accidentStatuses": [
      {
        "id": 1,
        "title": "new",
        "type": "accident"
      },
      {
        "id": 2,
        "title": "assigned",
        "type": "doctor"
      },
      {
        "id": 3,
        "title": "in_progress",
        "type": "doctor"
      },
      {
        "id": 4,
        "title": "sent",
        "type": "doctor"
      },
      {
        "id": 5,
        "title": "paid",
        "type": "doctor"
      }
    ],
    "accidentTypes": [
      {
        "id": 1,
        "title": "insurance",
        "description": ""
      },
      {
        "id": 2,
        "title": "non-insurance",
        "description": ""
      }
    ],
    "visitTimeRanges": [
      "2022-04-18>2022-04-22"
    ],
    "doctorServices": [
      {
        "id": 1,
        "title": "service1",
        "description": "",
        "status": "active",
        "diseases": [],
        "type": "director"
      }
    ],
    "doctorSurveys": [
      {
        "id": 1,
        "title": "Survey1",
        "description": "",
        "status": "active",
        "diseases": [],
        "type": "director"
      }
    ],
    "doctorDiagnostics": [
      {
        "id": 1,
        "title": "diagnostic 1",
        "description": "",
        "diagnosticCategoryId": 0,
        "status": "active",
        "diseases": [],
        "type": "director"
      }
    ]
  },
  "fields": [
    {
      "id": "npp",
      "title": "Npp",
      "order": "desc",
      "sort": 1
    },
    {
      "id": "patient",
      "title": "Patient",
      "order": "asc",
      "sort": 2
    },
    {
      "id": "city",
      "title": "City",
      "order": "asc",
      "sort": 3
    },
    {
      "id": "doctor-income",
      "title": "Doctor\'s fees",
      "order": "",
      "sort": 4
    },
    {
      "id": "assist-ref-num",
      "title": "Assistant Ref. Number",
      "order": "asc",
      "sort": 5
    }
  ]
}';
    }
}
