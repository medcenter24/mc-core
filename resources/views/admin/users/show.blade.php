@extends('admin.html')

@section('content')
    <div class="container white">

        <h1>@lang('content.user_info'): {{ $user->name }}</h1>

        @include('elements.errors')

        {{ $user->email }}

        <div class="row">
            <div class="col-sm-6">
                <h3>@lang('content.roles')</h3>
                <p>
                    @foreach($user->roles as $role)
                        {{ $role->title }}<br />
                    @endforeach
                </p>
            </div>
        </div>
    </div>
@endsection
