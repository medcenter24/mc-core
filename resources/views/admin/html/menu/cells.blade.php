@if(!isset($cell['role']) || \Roles::hasRole(auth()->user(), $cell['role']))

    @if(isset($cell['submenu']) && count($cell['submenu']))
        <li class="dropdown{{ isset($submenu) ? ' dropdown-submenu' : '' }}{{ isset($cell['active']) ? ' active': '' }}">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ $cell['name'] }}{!! $submenu or  ' <span class="caret"></span>' !!}</a>
            <ul class="dropdown-menu" role="menu">
                @foreach($cell['submenu'] as $submenu)
                    @include('admin.html.menu.cells', ['cell' => $submenu, 'submenu' => true ])
                @endforeach
            </ul>
        </li>
    
    @else
        <li{{ isset($cell['active']) ? ' class=active' : '' }}>
            <a href="{{ isset($cell['slug']) ? url($cell['slug']) : '#' }}">{{ isset($cell['name']) ? $cell['name'] : 'fake' }}@if(isset($cell['badge']))<span class="badge">{{ call_user_func($cell['badge']) }}</span>@endif</a>
        </li>
    @endif

@endif
