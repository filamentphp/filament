@php
    use Filament\Support\Enums\VerticalAlignment;

    $fromBreakpoint = $getFromBreakpoint();
    $verticalAlignment = $getVerticalAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-split',
                'fi-dense' => $isDense(),
                'fi-from-' . ($fromBreakpoint ?? 'default'),
                ($verticalAlignment instanceof VerticalAlignment) ? "fi-vertical-align-{$verticalAlignment->value}" : $verticalAlignment,
            ])
    }}
>
    @foreach ($getChildSchema()->getComponents() as $component)
        <div
            @class([
                'fi-growable' => $component->canGrow(),
            ])
        >
            {{ $component }}
        </div>
    @endforeach
</div>
