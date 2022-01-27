@extends('admin.html')

@section('content')
    <div class="container white m-2">
        <ul>
        @foreach($services as $service)
            <li>{{ $service->id }} {{ $service->title }}</li>
        @endforeach
        </ul>
    </div>
@endsection
