<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected $fillable = ['title', 'description', 'template', 'values'];
}
