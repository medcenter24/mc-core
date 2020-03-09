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

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Helpers\Date;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\UserService;

abstract class AbstractTransformer extends TransformerAbstract
{
    use ServiceLocatorTrait;

    protected const VAR_INT = 'int';
    protected const VAR_STRING = 'string';
    protected const VAR_FLOAT = 'float';
    protected const VAR_BOOL = 'bool';
    protected const VAR_DATE = 'date';

    /**
     * Mapped fields
     * @example ['guiFieldName' => 'storageFieldName']
     * @return array
     */
    abstract protected function getMap(): array;

    /**
     * To convert types : we need to define expected var types
     * @return array
     */
    protected function getMappedTypes(): array
    {
        return [
            'id' => self::VAR_INT,
        ]; // other string by default
    }

    /**
     * Returns transformed data
     * by default - just return key - val of expected fields
     * @example ['guiFieldName' => 'val']
     * @param Model $model
     * @return array
     */
    public function transform(Model $model): array
    {
        $transformed = [];
        foreach ($this->getMap() as $gui => $stored) {
            if (!is_string($gui)) {
                $gui = $stored;
            }
            $transformed[$gui] = $this->getVal($stored, $model);
        }
        return $transformed;
    }

    private function typedVal(string $valName, $value)
    {
        $varMap = $this->getMappedTypes();
        $type = self::VAR_STRING;
        if (array_key_exists($valName, $varMap)) {
            $type = $varMap[$valName];
        }
        switch ($type) {
            case self::VAR_STRING:
                $value = (string) $value;
                break;
            case self::VAR_BOOL:
                $value = (bool) $value;
                break;
            case self::VAR_FLOAT:
                $value = (float) $value;
                break;
            case self::VAR_INT:
                $value = (int) $value;
                break;
            case self::VAR_DATE:
                $value = Date::sysDate(
                    $value,
                    $this->getServiceLocator()->get(UserService::class)->getTimezone()
                );
                break;
        }
        return $value;
    }

    private function getVal(string $valName, Model $model)
    {
        $value = $model->getAttribute($valName);
        return $this->typedVal($valName, $value);
    }

    /**
     * replace keys with internal expectations
     * @example
     * ['userName' => 'Alex']
     * can be transformed to
     * ['user_name' => 'Alex'] or ['name' => 'Alex'] ...
     * @param array $data
     * @return array
     */
    public function inverseTransform(array $data): array
    {
        $transformed = [];
        foreach ($this->getMap() as $gui => $stored) {
            if (!is_string($gui)) {
                $gui = $stored;
            }
            if (array_key_exists($gui, $data)) {
                $transformed[$stored] = $data[$gui];
            }
        }
        return $transformed;
    }
}
