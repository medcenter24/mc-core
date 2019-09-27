@php
function printMenu ($menu) {
    foreach ( $menu as $item ) {
        if (array_key_exists('active', $item) && $item['active']) {
            echo '<li class="breadcrumb-item">';
            if(isset($item['slug'])) {
                echo '<a href="'.url($item['slug']).'">'.$item['name'].'</a>';
            } else {
                echo $item['name'];
            }
            echo '</li>';
            $menu = is_array($item) && array_key_exists('submenu', $item) ? $item['submenu'] : false;
            if ($menu) {
                printMenu($menu);
            }
            break;
        }
    }
}
@endphp

@if ( isset ($menuService) && count($menuService->asArray()) )
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="{{ url('admin') }}" class="breadcrumb-item">@lang('content.main')</a>
            </li>
            @php
                printMenu($menuService->asArray());
            @endphp
        </ol>
    </nav>
@endif
