<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Admin part just for the developer, to see where was last backup,
 * access to downloaded files, everything that I could need to.
 *
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    public function __construct()
    {
        view()->share('current_menu', '');
        //   view()->share('locations', config('translation-manager.locales'));
    }
}
