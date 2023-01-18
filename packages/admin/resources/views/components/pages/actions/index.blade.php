@props([
    'actions',
    'alignment' => 'left',
    'fullWidth' => false,
])

@if ($actions instanceof \Illuminate\Contracts\View\View)
    {{ $actions }}
@elseif (is_array($actions))
    @php
        $actions = array_filter(
            $actions,
            fn (\Filament\Pages\Actions\Action | \Filament\Pages\Actions\ActionGroup $action): bool => ! $action->isHidden(),
        );
    @endphp

    @if (count($actions))
        <div
            {{ $attributes->class([
                'filament-page-actions',
                'flex flex-wrap items-center gap-4' => ! $fullWidth,
                match ($alignment) {
                    'center' => 'justify-center',
                    'right' => 'flex-row-reverse space-x-reverse',
                    default => 'justify-start',
                } => ! $fullWidth,
                'grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]' => $fullWidth,
            ]) }}
        >
            {{ \Filament\Facades\Filament::renderHook('page.actions.start') }}
            
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
            
            {{ \Filament\Facades\Filament::renderHook('page.actions.end') }}
            
        </div>
    @endif
@endif
