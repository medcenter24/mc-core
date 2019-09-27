@if(isset($menuService))
    <div class="container">
        <!-- NavBar -->
        <nav class="navbar navbar-light navbar-admin navbar-expand-lg bg-light">
            <a href="{{ url('admin') }}" class="navbar-brand">@lang('content.project_name')</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse"
                    data-target="#navbarBackoffice"
                    aria-controls="navbarBackoffice"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbarBackoffice">
                <ul class="navbar-nav mr-auto">
                    @each('admin.html.menu.cells', $menuService->asArray(), 'cell')
                </ul>

                <ul class="navbar-nav mr-2">
                    <li class="nav-item">
                        <a href="{{ url('/admin') }}"
                           class="nav-link"
                           title="{{ trans('content.profile') }}">{{ auth()->user()->name }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('logout') }}"
                           class="nav-link"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        >@lang('content.logout')</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
        <!-- /NavBar -->
    </div>
@endif
