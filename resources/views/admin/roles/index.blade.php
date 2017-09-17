@extends('admin.html')

@section('content')
    <div class="container white">

        @include('elements.flash-message')

        <div class="row">
            <div class="col-sm-4">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('content.name')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td><strong>{{ $role->title }}</strong></td>
                            <td>@lang('content.delete')</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include('elements.errors')
        <form method="post" action="{{ action('Admin\RolesController@store') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="title" id="title" value="{{ old('role') }}"
                               autocomplete="off" title="{{ trans('content.role') }}"
                               placeholder="{{ trans('content.role') }}">
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-success">@lang('content.add')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
