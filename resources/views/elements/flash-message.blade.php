@if(Session::has('flash_message'))
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button class="close" type="button" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session('flash_message') }}
    </div>
@endif
