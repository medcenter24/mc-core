@if(!isset($cell['role']) || \Roles::hasRole(auth()->user(), $cell['role']))

    @if(isset($cell['submenu']) && count($cell['submenu']))
        @php
            $id = mt_rand(10000, 99999)
        @endphp
        <li class="nav-item dropdown{{ isset($cell['active']) ? ' active': '' }}">
            <a href="#" class="nav-link dropdown-toggle"
               data-toggle="dropdown"
               role="button"
               id="{{ $id }}"
               aria-haspopup="true"
               aria-expanded="false">{{ $cell['name'] }}{!! $submenu or  ' <span class="caret"></span>' !!}</a>
            <ul class="dropdown-menu" aria-labelledby="{{ $id }}">
                @foreach($cell['submenu'] as $submenu)
                    <a class="dropdown-item" href="/{{ $submenu['slug'] }}">{{ $submenu['name'] }}</a>
                @endforeach
            </ul>
        </li>
    
    @else
        <li class="nav-item{{ isset($cell['active']) ? ' active' : '' }}">
            <a class="nav-link"
                    href="{{ isset($cell['slug']) ? url($cell['slug']) : '#' }}">{{ isset($cell['name']) ? $cell['name'] : 'fake' }}@if(isset($cell['badge']))<span
                        class="badge">{{ call_user_func($cell['badge']) }}</span>@endif</a>
        </li>
    @endif

@endif
