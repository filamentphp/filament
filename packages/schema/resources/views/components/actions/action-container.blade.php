@foreach ($getActions() as $action)
    @if ($action->isVisible())
        {{ $action }}
    @endif
@endforeach
