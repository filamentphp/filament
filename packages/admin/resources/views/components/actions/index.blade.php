@props([
    'actions',
])

@if ($actions instanceof \Illuminate\Contracts\View\View)
    {{ $actions }}
@elseif (is_array($actions))
    @php
        $actions = array_filter(
            $actions,
            fn (\Filament\Pages\Actions\Action $action): bool => ! $action->isHidden(),
        );
    @endphp

    @if (count($actions))
        <div {{ $attributes->class([config("filament.styles.form_action_button_row")]) }}>
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
@endif
