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

namespace medcenter24\mcCore\App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use medcenter24\mcCore\App\Services\Entity\FormService;

/**
 * Form this is a template for the documents, which can be filled only with values data
 *  main part of this is template:
 *   - template has formatting, text, images
 *   - template can be changed in gui editor
 *  Examples
 *   template:
 * ```
 *     Good Morning :sex :firstName :lastName,
 *     I want to introduce you our new feature :featureName
 *
 *     Thank you for attention!
 * ```
 *   values:
 *      'sex,firstName,lastName,featureName'
 *
 *  And now we can use this template for any of our models:
 *  fe: model FeatureTemplate: title: feature 1, values: {firstName: 'Forest', lastName: 'Abigail', featureName: 'feature First'}
 *
 * Class Form
 * @package App
 */
class Form extends Model
{
    use SoftDeletes;

    protected $fillable = FormService::FILLABLE;
    protected $visible = FormService::VISIBLE;
}
