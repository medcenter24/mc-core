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

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Helpers\StrHelper;
use medcenter24\mcCore\App\Services\Core\Cache\ArrayCacheTrait;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;

class SurveyService extends AbstractModelService
{
    use FiltersTrait;
    use ArrayCacheTrait;

    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_DISEASE_ID = 'disease_id';
    public const FIELD_STATUS = 'status';
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_CREATED_BY = 'created_by';

    /**
     * Visible and selectable
     */
    public const STATUS_ACTIVE = 'active';
    /**
     * Visible but not selectable
     */
    public const STATUS_HIDDEN = 'hidden';
    /**
     * Hidden and not accessible
     */
    public const STATUS_DISABLED = 'disabled';

    /**
     * That can be modified
     */
    public const FILLABLE = [
        SurveyService::FIELD_TITLE,
        SurveyService::FIELD_DESCRIPTION,
        SurveyService::FIELD_CREATED_BY,
        SurveyService::FIELD_DISEASE_ID,
        SurveyService::FIELD_STATUS,
    ];

    /**
     * That can be updated
     */
    public const UPDATABLE = [
        SurveyService::FIELD_TITLE,
        SurveyService::FIELD_DESCRIPTION,
        SurveyService::FIELD_DISEASE_ID,
        SurveyService::FIELD_STATUS,
    ];

    /**
     * That can be viewed
     */
    public const VISIBLE = [
        SurveyService::FIELD_ID,
        SurveyService::FIELD_TITLE,
        SurveyService::FIELD_DESCRIPTION,
        SurveyService::FIELD_CREATED_BY,
        SurveyService::FIELD_DISEASE_ID,
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
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_DESCRIPTION => '',
            self::FIELD_DISEASE_ID => 0,
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
    public function create(array $data = []): Model
    {
        $data[self::FIELD_CREATED_BY] = auth()->user() ? auth()->user()->getAuthIdentifier() : 0;
        /** @var Survey $survey */
        $survey = parent::create($data);
        $list = $this->getCache('letteredSurveys');
        $list[StrHelper::getLetters($survey->getAttribute(self::FIELD_TITLE))] = $survey;
        $this->setCache('letteredSurveys', $list);
        return $survey;
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
