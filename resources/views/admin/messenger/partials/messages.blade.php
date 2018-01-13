<div class="media my-4">
    <img src="{{ asset('img/icons/man-user.svg') }}"
         alt="{{ $message->user->name }}" class="mr-3 text-muted">
    <div class="media-body">
        <h5 class="mt-0">{{ $message->user->name }}</h5>
        <p>{{ $message->body }}</p>
        <div class="text-muted">
            <small>Posted {{ $message->created_at->diffForHumans() }}</small>
        </div>
    </div>
</div>