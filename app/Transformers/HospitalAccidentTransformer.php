<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Helpers\Date;
use medcenter24\mcCore\App\Services\Entity\UserService;

class HospitalAccidentTransformer extends AccidentTransformer
{
    public function transform (Model $model): array
    {
        $fields = (new AccidentTransformer())->transform($model);
        $fields['status'] = $model->getAttribute('caseable')
            ? $model->getAttribute('caseable')->getAttribute('status')
            : '';
        $fields['accidentStatusId'] = $model->getAttribute('caseable')
            ? $model->getAttribute('caseable')->getAttribute('accident_status_id')
            : '';
        $fields['hospitalId'] = $model->getAttribute('caseable')
            ? $model->getAttribute('caseable')->getAttribute('hospital_id')
            : '';
        $fields['createdAt'] = Date::sysDate(
            $model->getAttribute('caseable')
                ? $model->getAttribute('caseable')->getAttribute('created_at')
                : null,
            $this->getServiceLocator()->get(UserService::class)->getTimezone()
        );
        return $fields;
    }
}
