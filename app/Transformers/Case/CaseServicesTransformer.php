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

namespace medcenter24\mcCore\App\Transformers\Case;

use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Services\Entity\ServiceService;

class CaseServicesTransformer extends TransformerAbstract
{
    #[ArrayShape([
        ServiceService::FIELD_ID => "mixed",
        ServiceService::FIELD_TITLE => "mixed",
        ServiceService::FIELD_DESCRIPTION => "mixed",
        ServiceService::FIELD_STATUS => "mixed",
        'sort' => "mixed"
    ])] public function transform($service): array {
        return [
            ServiceService::FIELD_ID          => $service->id,
            ServiceService::FIELD_TITLE       => $service->title,
            'sort'                            => $service->sort,
        ];
    }
}
