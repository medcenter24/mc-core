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

namespace medcenter24\mcCore\App\Services\Core\Http\Builders;


use Illuminate\Support\Collection;

abstract class RequestBuilder
{
    private const FIELDS = 'fields';

    public const FIELD_NAME = 'field';
    public const FIELD_VALUE = 'value';

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Collection
     */
    private $fields;

    public function inject(array $config): void
    {
        $this->config = $config;
    }

    protected function getFieldValue(string $fieldName, $default = ''): string
    {
        $field = $this->getFields()->firstWhere(self::FIELD_NAME, '=', $fieldName);
        return $field ? $field[self::FIELD_VALUE] : $default;
    }

    protected function getConfig(): array
    {
        return $this->config;
    }

    protected function getFields(): Collection
    {
        if (!$this->fields) {
            $this->fields = collect();
            if (array_key_exists(self::FIELDS, $this->getConfig()) && is_array($this->getConfig()[self::FIELDS])) {
                /** @var Collection $fields */
                foreach ($this->getConfig()[self::FIELDS] as $field) {
                    if (is_array($field)
                        && array_key_exists(self::FIELD_NAME, $field)
                        && array_key_exists(self::FIELD_VALUE, $field)
                    ) {
                        $this->fields->push($field);
                    }
                }
            }
        }
        return $this->fields;
    }
}
