<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link id="favicon" rel="icon" type="image/png" sizes="64x64"
              href="data:image/png;base64,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">

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
