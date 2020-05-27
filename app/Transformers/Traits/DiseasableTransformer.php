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

namespace medcenter24\mcCore\App\Transformers\Traits;


use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\Disease;
use medcenter24\mcCore\App\Services\Entity\DiseaseService;
use medcenter24\mcCore\App\Transformers\DiseaseTransformer;

trait DiseasableTransformer
{
    protected function inverseDiseasesTransform(array $transformed): array
    {
        if (isset($data['diseases']) && is_array($data['diseases'])) {
            $transformed['diseases'] = [];
            foreach ($data['diseases'] as $disease) {
                if (isset($disease['id'])) {
                    $transformed['diseases'][] = $this->getDiseaseService()->first([DiseaseService::FIELD_ID => $disease['id']]);
                }
            }
        }
        return $transformed;
    }

    private function getDiseaseService(): DiseaseService
    {
        return $this->getServiceLocator()->get(DiseaseService::class);
    }

    protected function getTransformedDiseases(Collection $diseases): array
    {
        $diseaseTransformer = new DiseaseTransformer();
        $res = [];
        $diseases->each(static function (Disease $disease) use ($diseaseTransformer, &$res) {
            $res[] = $diseaseTransformer->transform($disease);
        });
        return $res;
    }
}
