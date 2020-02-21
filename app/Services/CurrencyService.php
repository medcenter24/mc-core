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

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\FinanceCurrency;

class CurrencyService extends AbstractModelService
{
    public const FIELD_TITLE = 'title';
    public const FIELD_CODE = 'code';
    public const FIELD_ICO = 'ico';
    public const FIELD_MARKERS = 'markers';

    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_CODE,
        self::FIELD_ICO,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_CODE,
        self::FIELD_ICO,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_CODE,
        self::FIELD_ICO,
    ];

    /**
     * @var FinanceCurrency
     */
    private $defaultCurrency;

    private $defaultCurrencies = [
        [self::FIELD_TITLE => 'Euro', self::FIELD_CODE => 'eu', self::FIELD_ICO => 'fa fa-euro', self::FIELD_MARKERS => ['€']],
        [self::FIELD_TITLE => 'Dollar', self::FIELD_CODE => 'us', self::FIELD_ICO => 'fa fa-dollar', self::FIELD_MARKERS => ['$']],
    ];

    /**
     * Converts value to the other currencies
     * @param int $val
     * @param FinanceCurrency $currency
     * @param FinanceCurrency|null $toCurrency
     * @return int
     * @throws InconsistentDataException
     */
    public function convertCurrency($val, FinanceCurrency $currency, FinanceCurrency $toCurrency = null)
    {
        if (isset($toCurrency) && $toCurrency->getAttribute(self::FIELD_CODE) !== $currency->getAttribute(self::FIELD_CODE)) {
            throw new InconsistentDataException('Currency Convert has not been implemented yet');
        }
        // when it will be more supported currencies - needs to be implemented CurrencyConverterService
        return $val;
    }

    public function getDefaultCurrency(): FinanceCurrency
    {
        if (!$this->defaultCurrency) {
            $data = current($this->defaultCurrencies);
            unset($data[self::FIELD_MARKERS]);
            $this->defaultCurrency = $this->firstOrCreate($data);
        }

        return $this->defaultCurrency;
    }

    public function byCode(string $currencyCode): FinanceCurrency
    {
        /** @var FinanceCurrency $currency */
        $currency = $this->firstOrCreate([
            self::FIELD_CODE => $currencyCode,
        ]);
        return $currency;
    }

    /**
     * Name of the Model (ex: City::class)
     * @return string
     */
    protected function getClassName(): string
    {
        return FinanceCurrency::class;
    }

    /**
     * Initialize defaults to avoid database exceptions
     * (different storage have different rules, so it is correct to set defaults instead of nothing)
     * @return array
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_CODE => '',
            self::FIELD_ICO => '',
        ];
    }

    /**
     * @param string $marker
     * @return FinanceCurrency|null
     */
    public function byMarker(string $marker): ?FinanceCurrency
    {
        $data = null;
        foreach ($this->defaultCurrencies as $defaultCurrency) {
            if (in_array($marker, $defaultCurrency['markers'], true)) {
                $data = $defaultCurrency;
            }
        }
        $currency = null;
        if ($data) {
            unset($data['markers']);
            /** @var FinanceCurrency $currency */
            $currency = $this->firstOrCreate($data);
        }

        if (!$currency) {
            $currency = $this->getDefaultCurrency();
        }

        return $currency;
    }

    protected function getUpdatableFields(): array
    {
        return self::UPDATABLE;
    }
}
