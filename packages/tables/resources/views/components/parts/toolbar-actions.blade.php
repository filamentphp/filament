@php
    $toolbarActions = $table->getVisibleToolbarActions();
@endphp

@if ((! $table->isReordering()) && count($toolbarActions))
    @foreach ($toolbarActions as $action)
        {{ $action }}
    @endforeach
@endif
