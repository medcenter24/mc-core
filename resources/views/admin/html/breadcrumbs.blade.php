@inject('menu', 'App\Helpers\Admin\Menu')

<div class="container offset-top10 offset-bottom10 text-muted">

    <?php
    if (empty($current_menu)){
        $current_menu = $menu->get_current_menu();
    }

    if (!empty($current_menu)) {
        $submenu = $menu->menu($current_menu);

        if ($current_menu != 1) {
            echo '<a href="'.url('admin').'" class="breadcrumb">'.trans('content.main').'</a> &raquo;';
        }

        foreach (explode('.', $current_menu) as $item) {
            echo '<span class="breadcrumb">';
            if(isset($submenu[$item]['slug']))
                echo '<a href="'.url($submenu[$item]['slug']).'">'. $submenu[$item]['name'] .'</a>';
            else echo $submenu[$item]['name'];

            echo '</span>';

            if (isset($submenu[$item]['submenu'])) {
                echo ' &raquo; ';
                $submenu = $submenu[$item]['submenu'];
            }
        }
    }
    ?>
</div>
