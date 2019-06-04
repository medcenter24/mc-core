@if ( !empty($menuService->asArray()) )
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="{{ url('admin') }}" class="breadcrumb-item">@lang('content.main')</a>
            </li>
            @foreach ( $menuService->asArray() as $item )
                @if (array_key_exists('active', $item) && $item['active'])
                    <li class="breadcrumb-item">
                        @if(isset($item['slug']))
                            <a href="{{ url($item['slug']) }}">{{ $item['name'] }}</a>
                        @else
                            {{ $item['name'] }}
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
