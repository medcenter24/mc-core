<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers\Admin;

use \App\Helpers\Menu as AbstractMenu;

class Menu extends AbstractMenu
{

    /**
     * @return array
     */
    protected function getMenu() {
        return [
            '1' => [
                'name' => trans('content.users'),
                'submenu' => [
                    '10' => [
                        'name' => trans('content.list'),
                        'slug' => 'admin/users',
                    ],
                    '20' => [
                        'name' => trans('content.roles'),
                        'slug' => 'admin/roles',
                    ],
                ],
                'role' => 'admin'
            ],
            '2' => [
                'name' => trans('content.preview'),
                'submenu' => [
                    '10' => [
                        'name' => trans('content.case_report'),
                        'slug' => 'admin/preview/caseReport',
                    ]
                ],
            ],
            '3' => [
                'name' => trans('content.translate'),
                'slug' => 'admin/translation',
            ],
        ];
    }
}
