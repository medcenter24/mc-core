<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script>
          window.Laravel = {csrfToken: '{{ csrf_token() }}'};
        </script>

        <link href="{{ elixir('/css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">

                <div class="row">
                    <div class="col-lg-12">
                        @include('admin.html.menu')
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        @include('admin.html.breadcrumbs')
                    </div>
                </div>

                @yield('content')

                <div id="app">
                    @stack('components')
                </div>

                @include('admin.html.footer')
            </div>
        </div>

        <script src="{{ elixir('/js/app.js') }}"></script>

    </body>
</html>
