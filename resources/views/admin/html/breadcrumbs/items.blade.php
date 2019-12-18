@foreach ( $menu as $item )
    @if (array_key_exists('active', $item) && $item['active'])
        <li class="breadcrumb-item">
        @if(isset($item['slug']))
            <a href="{{ url($item['slug']) }}">{{ $item['name'] }}</a>
        @else
            {{ $item['name'] }}
        @endif
        </li>
        @php
        $menu = isset($item) && is_array($item) && array_key_exists('submenu', $item) ? $item['submenu'] : false;
        @endphp
        @if ($menu)
            @include('admin.html.breadcrumbs.items', ['menu' => $menu])
        @endif
        @break
    @endif
@endforeach
