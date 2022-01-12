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
declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Helpers\StrHelper;
use medcenter24\mcCore\App\Services\Core\Cache\ArrayCacheTrait;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;
use medcenter24\mcCore\App\Services\Entity\Contracts\CreatedByField;
use medcenter24\mcCore\App\Services\Entity\Contracts\StatusableService;
use medcenter24\mcCore\App\Services\Entity\Traits\Access;
use medcenter24\mcCore\App\Services\Entity\Traits\Diseasable;

class SurveyService extends AbstractModelService implements StatusableService, CreatedByField
{
    use FiltersTrait;
    use ArrayCacheTrait;
    use Access;
    use Diseasable;

    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_STATUS = 'status';

    /**
     * That can be modified
     */
    public const FILLABLE = [
        SurveyService::FIELD_TITLE,
        SurveyService::FIELD_DESCRIPTION,
        CreatedByField::FIELD_CREATED_BY,
        SurveyService::FIELD_STATUS,
    ];

    /**
     * That can be updated
     */
    public const UPDATABLE = [
        SurveyService::FIELD_TITLE,
        SurveyService::FIELD_DESCRIPTION,
        SurveyService::FIELD_STATUS,
    ];

    /**
     * That can be viewed
     */
    public const VISIBLE = [
        SurveyService::FIELD_ID,
        SurveyService::FIELD_TITLE,
        SurveyService::FIELD_DESCRIPTION,
        CreatedByField::FIELD_CREATED_BY,
        SurveyService::FIELD_STATUS,
    ];

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return Survey::class;
    }

    /**
     * @return array
     */
    #[ArrayShape([self::FIELD_TITLE       => "string",
                  self::FIELD_DESCRIPTION => "string",
                  self::FIELD_CREATED_BY  => "int",
                  self::FIELD_STATUS      => "string"
    ])] protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_DESCRIPTION => '',
            self::FIELD_CREATED_BY => 0,
            self::FIELD_STATUS => self::STATUS_ACTIVE,
        ];
    }

    /**
     * @return Collection
     */
    private function getSurveys(): Collection
    {
        if (!$this->hasCache('surveys')) {
            $this->setCache('surveys', Survey::all());
        }
        return $this->getCache('surveys');
    }

    /**
     * @return array
     */
    private function getLetteredSurveys(): array
    {
        if (!$this->hasCache('letteredSurveys')) {
            $surveys = [];
            $this->getSurveys()->each(static function (Survey $survey) use (&$surveys) {
                $surveys[StrHelper::getLetters($survey->getAttribute(self::FIELD_TITLE))] = $survey;
            });
            $this->setCache('letteredSurveys', $surveys);
        }
        return $this->getCache('letteredSurveys');
    }

    /**
     * @param string $title
     * @return Survey|null
     */
    protected function getByTitleLetters(string $title): ?Survey
    {
        $matchTitle = StrHelper::getLetters($title);

        if (array_key_exists($matchTitle, $this->getLetteredSurveys())) {
            return $this->getLetteredSurveys()[$matchTitle];
        }
        return null;
    }

    /**
     * @param array $data
     * @return Model|Survey
     */
    public function create(array $data = []): Model|Survey
    {
        $data[self::FIELD_CREATED_BY] = auth()->user() ? auth()->user()->getAuthIdentifier() : 0;
        /** @var Survey $survey */
        $survey = parent::create($data);
        $list = $this->getCache('letteredSurveys');
        $list[StrHelper::getLetters($survey->getAttribute(self::FIELD_TITLE))] = $survey;
        $this->assignDiseases($survey, $data);
        $this->setCache('letteredSurveys', $list);
        return $survey;
    }

    public function findAndUpdate(array $filterByFields, array $data): Model
    {
        $diagnostic = parent::findAndUpdate($filterByFields, $data);
        $this->assignDiseases($diagnostic, $data);
        return $diagnostic;
    }

    /**
     * @param array $params
     * @return Survey
     */
    public function byTitleLettersOrCreate(array $params): Survey
    {
        $survey = $this->getByTitleLetters($params[self::FIELD_TITLE]);
        if (!$survey) {
            $survey = $this->create($params);
        }
        return $survey;
    }
}
