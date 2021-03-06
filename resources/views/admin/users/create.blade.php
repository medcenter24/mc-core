@extends('admin.html')

@section('content')
    <div class="container white m-2">

        @include('elements.errors')

        <form method="post" action="{{ action('Admin\UsersController@store') }}">
            @include('admin.users._form', ['submit_button' => trans('content.add')])
        </form>
    </div>
@endsection
