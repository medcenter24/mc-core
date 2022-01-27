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

use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\RoleService;

class MenuService
{
    /**
     * Pages without menu highlighting
     */
    protected array $excluded = [];

    /**
     * Mapping
     */
    protected array $menuMap = [];

    /**
     * Mapping of the menu to show
     */
    protected array $filteredMenu = [];

    private string $title = '';

    public function __construct(private RoleService $roleService)
    {
    }

    /**
     * Returns menu which is ready to be shown
     */
    public function asArray(): array {
        return $this->filteredMenu();
    }

    /**
     * Some kind of the menu cache
     */
    private function filteredMenu(): array
    {
        if (!count($this->filteredMenu)) {
            $this->filteredMenu = $this->getMappedMenu($this->menuMap);
        }
        return $this->filteredMenu;
    }

    /**
     * Overwork menu
     * @param array $menu
     * @return array
     */
    private function getMappedMenu(array $menu): array
    {
        foreach ($menu as $key => $item) {

            // use only menu with access
            if (array_key_exists('role', $item)) {
                if (!$this->roleService->hasRole(auth()->user(), $item['role'])) {
                    unset($menu[$key]);
                    continue;
                }
            }
            // translate
            if (array_key_exists('name', $item)) {
                $menu[$key]['name'] = trans($item['name']);
            }

            // filter submenus
            if (array_key_exists('submenu', $item)) {
                $menu[$key]['submenu'] = $this->getMappedMenu($item['submenu']);
            }
        }

        return $menu;
    }

    /**
     * highlight current menu
     * @param string $currentMenu [may be '1.10.10' - that mean: highlight all menu and submenu]
     * @throws InconsistentDataException
     */
    public function markCurrentMenu(string $currentMenu = ''): void
    {
        // update title and menu array
        $this->title = '';
        $this->filteredMenu = [];
        $this->filteredMenu();
        $cells = explode('.', $currentMenu);

        $menu = &$this->filteredMenu;
        foreach ($cells as $cell) {
            if (!array_key_exists($cell, $menu)) {
                logger('Admin Menu undefined current_menu: ' . $currentMenu);
            }

            $menu[$cell]['active'] = true;
            if (!array_key_exists('name', $menu[$cell])) {
                throw new InconsistentDataException('Name field not found in the ' . $cell . ' for the array menu: ' . print_r($menu, 1));
            }
            $this->title .= (empty($this->title) ? '' : ' ::: ') . $menu[$cell]['name'];
            if (array_key_exists('submenu', $menu[$cell])) {
                $menu = &$menu[$cell]['submenu'];
            }
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
