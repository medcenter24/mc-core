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

namespace medcenter24\mcCore\App\Services\Menu;


class AdminMenuService extends MenuService
{
    /**
     * @var array Menu Map
     */
    protected array $menuMap = [
        1 => [
            'name' => 'content.users',
            'role' => 'admin',
            'submenu' => [
                '10' => [
                    'name' => 'content.list',
                    'slug' => 'admin/users',
                ],
                '20' => [
                    'name' => 'content.roles',
                    'slug' => 'admin/roles',
                ],
                '30' => [
                    'name' => 'content.invites',
                    'slug' => 'admin/invites',
                ]
            ],
        ],
        2 => [
            'role' => 'admin',
            'name' => 'content.preview',
            'submenu' => [
                '10' => [
                    'name' => 'content.case_report',
                    'slug' => 'admin/preview/caseReport',
                ],
                '20' => [
                    'name' => 'content.case_history',
                    'slug' => 'admin/preview/caseHistory',
                ],
                '30' => [
                    'name' => 'content.messenger',
                    'slug' => 'admin/preview/messenger',
                ],
            ],
        ],
        /*3 => [
            'role' => 'admin',
            'name' => 'content.translate',
            'slug' => 'admin/translation',
        ],*/
        4 => [
            'role' => 'admin',
            'name' => 'content.messengers',
            'submenu' => [
                '10' => [
                    'name' => 'content.telegram',
                    'slug' => 'admin/preview/telegram',
                ],
                '20' => [
                    'name' => 'content.slack',
                    'slug' => 'admin/preview/slack',
                ],
            ]
        ],
        5 => [
            'role' => 'admin',
            'name' => 'content.system',
            'submenu' => [
                '10' => [
                    'name' => 'content.models',
                    'slug' => 'admin/system/models',
                ],
            ],
        ],
        7 => [
            'role' => 'admin',
            'name' => 'content.entities',
            'submenu' => [
                '10' => [
                    'name' => 'content.doctor_service',
                    'slug' => 'admin/entity/doctor-service',
                ],
                '20' => [
                    'name' => 'content.accident_status',
                    'slug' => 'admin/entity/accident-status',
                ],
            ]
        ],
    ];
}
