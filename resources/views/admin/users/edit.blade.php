@extends('admin.html')

@section('content')
    <div class="container white m-2">

        <h1>@lang('content.edit'): {{ $user->name }}</h1>

        @include('elements.errors')

        <form method="post" action="{{ action('Admin\UsersController@update', [$user->id]) }}">
            {{ method_field('patch') }}
            @include('admin.users._form', ['submit_button' => trans('content.save')])
        </form>

    </div>
@endsection
