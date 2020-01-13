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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services;


use Illuminate\Support\Collection;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Helpers\StrHelper;
use medcenter24\mcCore\App\Services\Core\Cache\ArrayCacheTrait;
use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\DoctorLayer\FiltersTrait;

class DoctorSurveyService extends AbstractModelService
{
    use FiltersTrait;
    use ArrayCacheTrait;

    public const STATUS_ACTIVE = 'active';

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return DoctorSurvey::class;
    }

    /**
     * @return array
     */
    protected function getRequiredFields(): array
    {
        return [
            'title' => '',
            'description' => '',
            'disease_code' => '',
        ];
    }

    /**
     * @return Collection
     */
    private function getSurveys(): Collection
    {
        if (!$this->hasCache('surveys')) {
            $this->setCache('surveys', DoctorSurvey::all());
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
            $this->getSurveys()->each(static function (DoctorSurvey $survey) use (&$surveys) {
                $surveys[StrHelper::getLetters($survey->getAttribute('title'))] = $survey;
            });
            $this->setCache('letteredSurveys', $surveys);
        }
        return $this->getCache('letteredSurveys');
    }

    /**
     * @param string $title
     * @return DoctorSurvey|null
     */
    protected function getByTitleLetters(string $title): ?DoctorSurvey
    {
        $matchTitle = StrHelper::getLetters($title);

        if (array_key_exists($matchTitle, $this->getLetteredSurveys())) {
            return $this->getLetteredSurveys()[$matchTitle];
        }
        return null;
    }

    /**
     * @param array $data
     * @return Model|DoctorSurvey
     */
    public function create(array $data = []): Model
    {
        /** @var DoctorSurvey $survey */
        $survey = parent::create($data);
        $list = $this->getCache('letteredSurveys');
        $list[StrHelper::getLetters($survey->getAttribute('title'))] = $survey;
        $this->setCache('letteredSurveys', $list);
        return $survey;
    }

    /**
     * @param array $params
     * @return DoctorSurvey
     */
    public function byTitleLettersOrCreate(array $params): DoctorSurvey
    {
        $survey = $this->getByTitleLetters($params['title']);
        if (!$survey) {
            $survey = $this->create($params);
        }
        return $survey;
    }
}
