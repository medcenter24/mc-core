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

namespace medcenter24\mcCore\App\Services\Entity\Traits;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Disease;

trait Diseasable
{
    /**
     * @param Model $model
     * @param array $data
     */
    private function assignDiseases(Model $model, array $data): void
    {
        $model->diseases()->detach();
        if (array_key_exists('diseases', $data)) {
            $diseases = $data['diseases'];
            $ids = [];
            foreach ($diseases as $disease) {
                $ids[] = $this->getId($disease);
            }
            $model->diseases()->attach($ids);
        }
        $model->save();
    }

    private function getId($disease): int
    {
        $id = 0;
        if ($disease instanceof Disease) {
            $id = $disease->getAttribute('id');
        } elseif (is_array($disease) && array_key_exists('id', $disease)) {
            $id = $disease['id'];
        }
        return (int) $id;
    }
}
