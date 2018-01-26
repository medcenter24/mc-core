@extends('admin.html')

@section('content')
    <div class="container white m-2">
        <div class="row offset-bottom10">
            <div class="col-sm-4">
                <a href="{{ url('admin/users/create') }}" class="btn btn-success">+ @lang('content.new_user')</a>
            </div>
            <div class="col-sm-8">
                @include('elements.flash-message')
            </div>
        </div>

        <div class="row">
            <div class="col-sm-7">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('content.name')</th>
                        <th>@lang('content.role')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>@foreach($user->roles as $role){{$role->title}} @endforeach</td>
                            <td>
                                <a href="{{ url('admin/users/'.$user->id.'/edit') }}">@lang('content.edit')</a>
                                <a href="{{ url('admin/users/'.$user->id) }}">@lang('content.view')</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
