@if ( isset ($menuService) && !empty($menuService->asArray()) )
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="{{ url('admin') }}" class="breadcrumb-item">@lang('content.main')</a>
            </li>
            @php
                $menu = $menuService->asArray();
                while ($menu) {
                    foreach ( $menu as $item ) {
                        if (array_key_exists('active', $item) && $item['active']) {
                            echo '<li class="breadcrumb-item">';
                            if(isset($item['slug'])) {
                                echo '<a href="'.url($item['slug']).'">'.$item['name'].'</a>';
                            } else {
                                echo $item['name'];
                            }
                            echo '</li>';
                        }
                    }
                    $menu = array_key_exists('submenu', $item) ? $item['submenu'] : false;
                }
            @endphp
        </ol>
    </nav>
@endif
