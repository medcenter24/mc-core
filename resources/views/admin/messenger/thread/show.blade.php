@extends('admin.html')

@section('content')

    <div class="container white p-2">
        <h3>{{ $thread->subject }}</h3>

        @each('admin.messenger.partials.messages', $thread->messages, 'message')

        <form method="post" action="{{ action('Admin\Messenger\ThreadController@createMessage', ['id' => $thread->id]) }}">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="identifier">User ID</label>
                <input type="text" class="form-control" name="identifier" id="identifier" value="{{ auth()->user()->id }}">
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea type="text" class="form-control" name="text" id="message"></textarea>
            </div>

            <button class="btn btn-info" type="submit">Send</button>
        </form>
    </div>

@stop
