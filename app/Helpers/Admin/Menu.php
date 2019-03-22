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

namespace App\Helpers\Admin;

use \App\Helpers\Menu as AbstractMenu;

class Menu extends AbstractMenu
{

    /**
     * @return array
     */
    protected function getMenu(): array {
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
                    '30' => [
                        'name' => trans('content.invites'),
                        'slug' => 'admin/invites',
                    ]
                ],
                'role' => 'admin'
            ],
            '2' => [
                'name' => trans('content.preview'),
                'submenu' => [
                    '10' => [
                        'name' => trans('content.case_report'),
                        'slug' => 'admin/preview/caseReport',
                    ],
                    '20' => [
                        'name' => trans('content.case_history'),
                        'slug' => 'admin/preview/caseHistory',
                    ],
                    '30' => [
                        'name' => trans('content.messenger'),
                        'slug' => 'admin/preview/messenger',
                    ],
                ],
            ],
            '3' => [
                'name' => trans('content.translate'),
                'slug' => 'admin/translation',
            ],
            '4' => [
                'name' => trans('content.telegram'),
                'submenu' => [
                    '10' => [
                        'name' => trans('content.preview'),
                        'slug' => 'admin/preview/telegram',
                    ],
                ]
            ]
        ];
    }
}
