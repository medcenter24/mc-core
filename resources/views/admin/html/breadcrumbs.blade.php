@inject('menu', 'App\Helpers\Admin\Menu')

@php
if (empty($current_menu)){
    $current_menu = $menu->get_current_menu();
}
@endphp

@if ( !empty($current_menu) )
    @php
    $submenu = $menu->menu($current_menu);
    @endphp

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">

        @if ( $current_menu !== 1 )
            <li href="{{ url('admin') }}" class="breadcrumb-item">@lang('content.main')</li>
        @endif

        @foreach ( explode('.', $current_menu) as $item )
            <li class="breadcrumb-item">
            @if(isset($submenu[$item]['slug']))
                <a href="{{ url($submenu[$item]['slug']) }}">{{ $submenu[$item]['name'] }}</a>
            @else
                {{ $submenu[$item]['name'] }}
            @endif
            </li>

            @if (isset($submenu[$item]['submenu']))
                @php
                    $submenu = $submenu[$item]['submenu'];
                @endphp
            @endif
        @endforeach
        </ol>
    </nav>
@endif
