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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Models\Scenario\ScenarioModel;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\ScenarioService;
use medcenter24\mcCore\App\Services\Scenario\StoryService;
use medcenter24\mcCore\App\Transformers\ScenarioTransformer;

class CaseStoryController extends ApiController
{
    /**
     * Load scenario for the current accident
     * @param int $id
     * @param AccidentStatusService $accidentStatusesService
     * @param ScenarioService $scenariosService
     * @param StoryService $storyService
     * @param AccidentService $accidentService
     * @return Response
     */
    public function story(
        int $id,
        AccidentStatusService $accidentStatusesService,
        ScenarioService $scenariosService,
        StoryService $storyService,
        AccidentService $accidentService
    ): Response
    {
        /** @var Accident $accident */
        $accident = $accidentService->first([AccidentService::FIELD_ID => $id]);

        if (!$accident) {
            $this->response->errorNotFound();
        }

        $scenario = new ScenarioModel(
            $accidentStatusesService,
            $scenariosService->getScenarioByTag($accident->getAttribute(AccidentService::FIELD_CASEABLE_TYPE))
        );

        return $this->response->collection(
            $storyService->init($accident->getAttribute('history'), $scenario)->getStory(),
            new ScenarioTransformer()
        );
    }
}
