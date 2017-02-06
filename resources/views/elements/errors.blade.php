@if($errors->any())
    <ul class="alert alert-danger errors-block">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
