@php
    use Filament\Support\Enums\VerticalAlignment;

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
                'fi-in-split flex gap-6',
                match ($getFromBreakpoint()) {
                    'sm' => 'flex-col sm:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'sm:items-center',
                        VerticalAlignment::End => 'sm:items-end',
                        default => 'sm:items-start',
                    },
                    'md' => 'flex-col md:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'md:items-center',
                        VerticalAlignment::End => 'md:items-end',
                        default => 'md:items-start',
                    },
                    'lg' => 'flex-col lg:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'lg:items-center',
                        VerticalAlignment::End => 'lg:items-end',
                        default => 'lg:items-start',
                    },
                    'xl' => 'flex-col xl:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'xl:items-center',
                        VerticalAlignment::End => 'xl:items-end',
                        default => 'xl:items-start',
                    },
                    '2xl' => 'flex-col 2xl:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => '2xl:items-center',
                        VerticalAlignment::End => '2xl:items-end',
                        default => '2xl:items-start',
                    },
                    default => match ($verticalAlignment) {
                        VerticalAlignment::Center => 'items-center',
                        VerticalAlignment::End => 'items-end',
                        default => 'items-start',
                    },
                },
            ])
    }}
>
    @foreach ($getChildComponentContainers() as $container)
        @foreach ($container->getComponents() as $component)
            <div
                @class([
                    'w-full flex-1' => $component->canGrow(),
                    match ($getFromBreakpoint()) {
                        'sm' => 'sm:sticky sm:top-[--sticky-offset] sm:self-start',
                        'md' => 'md:sticky md:top-[--sticky-offset] md:self-start',
                        'lg' => 'lg:sticky lg:top-[--sticky-offset] lg:self-start',
                        'xl' => 'xl:sticky xl:top-[--sticky-offset] xl:self-start',
                        '2xl' => '2xl:sticky 2xl:top-[--sticky-offset] 2xl:self-start',
                        default => 'sticky top-[--sticky-offset] self-start',
                    } => $component->isSticky(),
                ])
            >
                {{ $component }}
            </div>
        @endforeach
    @endforeach
</div>
