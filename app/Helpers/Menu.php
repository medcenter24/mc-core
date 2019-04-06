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

namespace App\Helpers;


abstract class Menu
{
    /**
     * String '1.10.10' - show which menu|submenu selected (for quick access for breadcrumbs or other things)
     * @var string
     */
    private $current_menu = '';

    /**
     * Pages without menu highlighting
     * @var array
     */
    protected $excluded = [];

    /**
     * @return array
     */
    abstract protected function getMenu(): array;

    /**
     * get selected menu
     * @return string
     */
    public function get_current_menu(): string
    {
        echo 'here';
        if (empty($this->current_menu)) {
            $this->set_current_menu();
        }
        
        return $this->current_menu;
    }

    /**
     * highlight current menu
     * @param string $current_menu [may be '1.10.10' - that mean: highlight all menu and submenu]
     */
    private function set_current_menu($current_menu = ''): void
    {
        $this->current_menu = '';
        $_m = $this->getMenu();
        $menu = &$_m;
        
        // set by current menu param
        if (!empty($current_menu)) {

            foreach (explode('.', $current_menu) as $cell) {

                if (!isset($menu[$cell])) {
                    logger('Admin Menu undefined current_menu: :cm', [':cm' => $current_menu]);
                    return;
                }

                $menu[$cell]['active'] = true;

                if (isset($menu[$cell]['submenu']))
                    $menu = &$menu[$cell]['submenu'];
            }

            $this->current_menu = $current_menu;

        } else {

            // set by current url
            $path = $this->path_by_url($this->getMenu());

            if ($path) {
                $this->set_current_menu($path);
            } else {
                if (!in_array(request()->path(), $this->excluded, true)) {
                    logger('Admin menu error: cant catch cell by url: ' . request()->path());
                }
                return;
            }
        }
    }

    private function path_by_url($menu): string
    {
        foreach ($menu as $key => $cell) {

            if (array_key_exists('submenu', $cell) && count($cell['submenu'])) {

                $_path = $this->path_by_url($cell['submenu']);

                if ($_path) {
                    return $key . '.' . $_path;
                }
            }

            if( array_key_exists('slug', $cell)
                && rtrim($cell['slug'], '/') === strtolower(rtrim(request()->path(), '/'))
            ) {
                return $key;
            }
        }

        return '';
    }

    /**
     * get menu array
     * @param string $current_menu
     * @return array
     */
    public function menu($current_menu = null): array
    {
        $this->set_current_menu($current_menu);

        $_m = $this->getMenu();
        $this->filterMenu($_m);
        
        return $this->getMenu();
    }

    /**
     * Filter menu by users roles
     * @param $menu
     * @return void
     */
    private function filterMenu(&$menu): void
    {
        foreach ($menu as $key => $item) {
            if (isset($item['role'])) {
                if (!\Roles::hasRole(auth()->user(), $item['role'])) {
                    unset($menu[$key]);
                }
            } elseif (isset($item['submenu'])) {
                $this->filterMenu($item['submenu']);
            }
        }
    }
}
