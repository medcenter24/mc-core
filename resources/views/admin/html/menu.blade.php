@inject('menu', 'App\Helpers\Admin\Menu')

<div class="container">
    <!-- NavBar -->
    <nav class="navbar navbar-default navbar-admin" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">@lang('Toggle')</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a href="{{ url('admin') }}" class="navbar-brand">@lang('content.project_name')</a>
                
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @each('admin.html.menu.cells', $menu->menu($current_menu), 'cell')
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li class="pull-right">
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <li class="pull-right">
                        <a href="{{ url('profile') }}" title="{{ trans('content.account') }}">{{ auth()->user()->name }}</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <!-- /NavBar -->
</div>
