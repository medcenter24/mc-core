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

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\Support\Core\Configurable;

/**
 * Extract Default html based tables from the array
 *
 * Example in the test: \Tests\Unit\Services\ExtractTableFromArrayServiceTest
 *
 * Class ExtractTableFromArrayService
 * @package medcenter24\mcCore\App\Services
 */
class ExtractTableFromArrayService extends Configurable
{
    /**
     * Extracted tables
     */
    const TABLES = 'tables';

    /**
     * All that left without tables
     */
    const CONTENT = 'content';

    /**
     * Configuration for extraction
     */
    const CONFIG_TABLE = 'table';
    const CONFIG_ROW = 'row';
    const CONFIG_CEIL = 'ceil';

    /**
     * Result will be table with rows [table1 = [row1, row2 => [col1, col2] ] ]
     *
     * @param array $resource
     * @return array
     * @throws \Exception
     */
    public function extract(array $resource)
    {
        if (
            !$this->hasOption(self::CONFIG_TABLE)
            || !$this->hasOption(self::CONFIG_ROW)
            || !$this->hasOption(self::CONFIG_CEIL)
        ) {
            throw new \Exception('All configurable parameters should be defined');
        }

        $result = [
            self::TABLES => [],
            self::CONTENT => []
        ];
        foreach ($resource as $key => $body) {
            if ($this->is($key, self::CONFIG_TABLE)) {
                $result[self::TABLES][] = $this->getRows($body);
            } else {
                if (is_array($body)) {
                    $container = $this->extract($body);
                    $result[self::TABLES] += $container[self::TABLES];
                    $result[self::CONTENT][$key] = $container[self::CONTENT];
                } else {
                    $result[self::CONTENT][$key] = $body;
                }
            }
        }

        return $result;
    }

    /**
     * Check if key is matched
     * @param $key
     * @param string $configValue
     * @return bool
     */
    private function is ($key, $configValue = '')
    {
        $key .= ''; // make it string
        $key = preg_replace('/:\d+$/', '', $key);
        return in_array($key, $this->getOption($configValue));
    }

    /**
     * Looking only for first (top) rows (exclude inner tables)
     *
     * @param $table
     * @return array
     */
    private function getRows($table)
    {
        $result = [];
        foreach ($table as $key => $element) {
            if ($this->is($key, self::CONFIG_ROW)) {
                $result[] = $this->getCols($element);
            } else {
                $result += $this->getRows($element);
            }
        }

        return $result;
    }

    private function getCols($row)
    {
        $result = [];
        foreach ($row as $key => $item) {
            if ($this->is($key, self::CONFIG_CEIL)) {
                $result[] = $item;
            } else {
                $result += $this->getCols($item);
            }
        }

        return $result;
    }
}
