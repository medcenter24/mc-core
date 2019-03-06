@if(!array_key_exists('role', $cell) || \Roles::hasRole(auth()->user(), $cell['role']))

    @if(array_key_exists('submenu', $cell) && count($cell['submenu']))
        @php
            $id = mt_rand(10000, 99999)
        @endphp
        <li class="nav-item dropdown{{ array_key_exists('active', $cell) ? ' active': '' }}">
            <a href="#" class="nav-link dropdown-toggle"
               data-toggle="dropdown"
               role="button"
               id="{{ $id }}"
               aria-haspopup="true"
               aria-expanded="false">{{ $cell['name'] }} <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="{{ $id }}">
                @foreach($cell['submenu'] as $submenu)
                    <a class="dropdown-item" href="/{{ $submenu['slug'] }}">{{ $submenu['name'] }}</a>
                @endforeach
            </ul>
        </li>
    
    @else
        <li class="nav-item{{ array_key_exists('active', $cell) ? ' active' : '' }}">
            <a class="nav-link"
                    href="{{ array_key_exists('slug', $cell) ? url($cell['slug']) : '#' }}">{{ array_key_exists('name', $cell) ? $cell['name'] : 'fake' }}@if(array_key_exists('badge', $cell))<span
                        class="badge">{{ call_user_func($cell['badge']) }}</span>@endif</a>
        </li>
    @endif

@endif
