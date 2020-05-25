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

namespace medcenter24\mcCore\App\Services\Entity;

use medcenter24\mcCore\App\Entity\Scenario;

class ScenarioService extends AbstractModelService
{

    public const FIELD_ACCIDENT_STATUS_ID = 'accident_status_id';
    public const FIELD_TAG = 'tag';
    public const FIELD_ORDER = 'order';
    public const FIELD_MODE = 'mode';

    public const FILLABLE = [
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_TAG,
        self::FIELD_ORDER,
        self::FIELD_MODE,
    ];

    public const UPDATABLE = [
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_TAG,
        self::FIELD_ORDER,
        self::FIELD_MODE,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_TAG,
        self::FIELD_ORDER,
        self::FIELD_MODE,
    ];

    /**
     * Don't need to load it twice
     * @var array
     */
    private $cache = [];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Scenario::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_ACCIDENT_STATUS_ID => 0,
            self::FIELD_TAG => '',
            self::FIELD_ORDER => 0,
            self::FIELD_MODE => '',
        ];
    }

    public function getScenarioByTag($tag = '')
    {
        if (!array_key_exists($tag, $this->cache)) {
            $this->cache[$tag] = Scenario::where('tag', $tag)
                ->orderBy('order')
                ->get();
        }
        return $this->cache[$tag];
    }
}