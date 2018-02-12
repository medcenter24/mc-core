@extends('admin.html')

@section('content')
    <div class="container white m-2">
        <div class="row">
            <div class="col-lg-9">
                @include('elements.flash-message')
            </div>
        </div>
        <form action="{{ action('Admin\InvitesController@store') }}" method="post" class="form form-inline mb-3">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="user" class="label mr-2">@lang('content.choose_user')</label>
                <select name="user" id="user" size="1" class="form-control mr-2">
                    @foreach($users as $user)
                        <option value="{{  $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-success btn-sm" type="submit">+ @lang('content.create_invite')</button>
        </form>

        @if($invites->count())
        <div class="row">
            <div class="col-12">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('content.user_id')</th>
                        <th>@lang('content.user_name')</th>
                        <th>@lang('content.token')</th>
                        <th>@lang('content.valid_from')</th>
                        <th>@lang('content.valid_to')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invites as $invite)
                        <tr>
                            <td>{{ $invite->user_id }}</td>
                            <td>{{ $invite->user->name }}</td>
                            <td>{{ $invite->token }}</td>
                            <td>{{ $invite->valid_from }}</td>
                            <td>{{ $invite->valid_to }}</td>
                            <td>
                                <form action="{{ action('Admin\InvitesController@destroy', [$invite->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button class="btn btn-danger btn-sm" type="submit">@lang('content.delete')</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="card card-body">
                @lang('content.no_invites')
            </div>
        @endif
    </div>
@endsection
