@extends('admin.html')

@section('content')
    <div class="container white m-2">
        <div class="row">
            <div class="col-lg-9">
                @include('elements.flash-message')
            </div>
        </div>

        <ul>
            @foreach($accidentStatuses as $status)
                <li>{{ $status->id }} {{ $status->title }}</li>
            @endforeach
        </ul>

        <form action="{{ action('Admin\Entity\AccidentStatusController@store') }}" method="post"
              class="form form-inline mb-3">
            {{ csrf_field() }}
            <button class="btn btn-success btn-sm" type="submit">@lang('content.regenerate')</button>
        </form>
    </div>
@endsection
