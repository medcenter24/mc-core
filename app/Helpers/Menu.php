<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
    abstract protected function getMenu();

    /**
     * get selected menu
     * @return string
     */
    public function get_current_menu()
    {
        if (empty($this->current_menu)) {
            $this->set_current_menu();
        }
        
        return $this->current_menu;
    }

    /**
     * highlight current menu
     * @param string $current_menu [may be '1.10.10' - that mean: highlight all menu and submenu]
     */
    private function set_current_menu($current_menu = '')
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
                if (!in_array(request()->path(), $this->excluded)) {
                    logger('Admin menu error: cant catch cell by url: ' . request()->path());
                }
                return;
            }
        }
    }

    private function path_by_url($menu)
    {

        foreach ($menu as $key => $cell) {

            if (isset($menu[$key]['submenu']) && count($menu[$key]['submenu'])) {

                $_path = $this->path_by_url($menu[$key]['submenu']);

                if ($_path) {
                    return $key . '.' . $_path;
                }
            }

            if (isset($cell['slug'])
                && rtrim($cell['slug'], '/') == rtrim(strtolower(request()->path()), '/')
            )
                return $key;
        }

        return false;
    }

    /**
     * get menu array
     * @param string $current_menu
     * @return array
     */
    public function menu($current_menu = null)
    {
        $this->set_current_menu($current_menu);

        $_m = $this->getMenu();
        $this->filterMenu($_m);
        
        return $this->getMenu();
    }

    /**
     * Filter menu by users roles
     * @param $menu
     */
    private function filterMenu(&$menu)
    {
        foreach ($menu as $key => $item) {
            if (isset($item['role'])) {
                if (!auth()->user()->hasRole($item['role'])) {
                    unset($menu[$key]);
                }
            } elseif (isset($item['submenu'])) {
                $this->filterMenu($item['submenu']);
            }
        }
    }
}
