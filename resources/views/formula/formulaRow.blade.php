@if ($collection)
@php $first = true; @endphp
@while ($collection->valid())
@php
    /** @var \App\Models\Formula\Operation $operation */
    $operation = $collection->current();
@endphp@if($operation->leftSignView(!$first)) {{ $operation->leftSignView() }} @endif{{ $operation->varView() }}@if($operation->rightSignView()) {{ $operation->rightSignView() }} @endif@php
    $collection->next();
    $first = false;
@endphp
@endwhile
@endif
