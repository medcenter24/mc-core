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

namespace medcenter24\mcCore\App\Services\Search\Model\Field\PostQueryField;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Services\Search\Model\Field\Request\SearchField;
use stdClass;

class NppPostQueryField extends AbstractPostQueryField
{
    private const PARAM_NPP = 'npp';

    public function apply(SearchField $searchField, Collection $result, int $position): Collection {
        $npp = 0;
        $newResult = collect();
        $result->each(static function ($row) use (&$npp, $position, $newResult) {
            $npp++;
            $i = 0;
            $std = new stdClass();
            foreach ($row as $key => $value) {
                if($position === $i++) {
                    $param = self::PARAM_NPP;
                    $std->$param = $npp;
                }
                $std->$key = $value;
            }
            if($position === $i) {
                $param = self::PARAM_NPP;
                $std->$param = $npp;
            }
            $newResult->push($std);
        });
        return $newResult;
    }
}
