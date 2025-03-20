@php
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Enums\HorizontalArrangement;

    $verticalAlignment = $getVerticalAlignment();
    $horizontalArrangement = $getHorizontalArrangement();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }

    if (! $horizontalArrangement instanceof HorizontalArrangement) {
        $horizontalArrangement = filled($horizontalArrangement) ? (HorizontalArrangement::tryFrom($horizontalArrangement) ?? $horizontalArrangement) : null;
    }
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-in-split flex ',
                'gap-' . $getGap() . ' ' => !!$getGap(),
                match ($getFromBreakpoint()) {
                    'sm' => 'flex-col sm:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'sm:items-center ',
                        VerticalAlignment::End => 'sm:items-end ',
                        default => 'sm:items-start ',
                    } . match ($horizontalArrangement) {
                        HorizontalArrangement::Start => 'sm:justify-start ',
                        HorizontalArrangement::Center => 'sm:justify-center ',
                        HorizontalArrangement::End => 'sm:justify-end ',
                        HorizontalArrangement::Between => 'sm:justify-between ',
                        HorizontalArrangement::Around => 'sm:justify-around ',
                        HorizontalArrangement::Evenly => 'sm:justify-evenly ',
                        HorizontalArrangement::Stretch => 'sm:justify-stretch ',
                        HorizontalArrangement::Baseline => 'sm:justify-baseline ',
                        default => 'sm:justify-normal '
                    },
                    'md' => 'flex-col md:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'md:items-center ',
                        VerticalAlignment::End => 'md:items-end ',
                        default => 'md:items-start ',
                    } . match ($horizontalArrangement) {
                        HorizontalArrangement::Start => 'md:justify-start ',
                        HorizontalArrangement::Center => 'md:justify-center ',
                        HorizontalArrangement::End => 'md:justify-end ',
                        HorizontalArrangement::Between => 'md:justify-between ',
                        HorizontalArrangement::Around => 'md:justify-around ',
                        HorizontalArrangement::Evenly => 'md:justify-evenly ',
                        HorizontalArrangement::Stretch => 'md:justify-stretch ',
                        HorizontalArrangement::Baseline => 'md:justify-baseline ',
                        default => 'md:justify-normal '
                    },
                    'lg' => 'flex-col lg:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'lg:items-center ',
                        VerticalAlignment::End => 'lg:items-end ',
                        default => 'lg:items-start ',
                    } . match ($horizontalArrangement) {
                        HorizontalArrangement::Start => 'lg:justify-start ',
                        HorizontalArrangement::Center => 'lg:justify-center ',
                        HorizontalArrangement::End => 'lg:justify-end ',
                        HorizontalArrangement::Between => 'lg:justify-between ',
                        HorizontalArrangement::Around => 'lg:justify-around ',
                        HorizontalArrangement::Evenly => 'lg:justify-evenly ',
                        HorizontalArrangement::Stretch => 'lg:justify-stretch ',
                        HorizontalArrangement::Baseline => 'lg:justify-baseline ',
                        default => 'lg:justify-normal '
                    },
                    'xl' => 'flex-col xl:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'xl:items-center ',
                        VerticalAlignment::End => 'xl:items-end ',
                        default => 'xl:items-start ',
                    } . match ($horizontalArrangement) {
                        HorizontalArrangement::Start => 'xl:justify-start ',
                        HorizontalArrangement::Center => 'xl:justify-center ',
                        HorizontalArrangement::End => 'xl:justify-end ',
                        HorizontalArrangement::Between => 'xl:justify-between ',
                        HorizontalArrangement::Around => 'xl:justify-around ',
                        HorizontalArrangement::Evenly => 'xl:justify-evenly ',
                        HorizontalArrangement::Stretch => 'xl:justify-stretch ',
                        HorizontalArrangement::Baseline => 'xl:justify-baseline ',
                        default => 'xl:justify-normal '
                    },
                    '2xl' => 'flex-col 2xl:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => '2xl:items-center ',
                        VerticalAlignment::End => '2xl:items-end ',
                        default => '2xl:items-start ',
                    } . match ($horizontalArrangement) {
                        HorizontalArrangement::Start => '2xl:justify-start ',
                        HorizontalArrangement::Center => '2xl:justify-center ',
                        HorizontalArrangement::End => '2xl:justify-end ',
                        HorizontalArrangement::Between => '2xl:justify-between ',
                        HorizontalArrangement::Around => '2xl:justify-around ',
                        HorizontalArrangement::Evenly => '2xl:justify-evenly ',
                        HorizontalArrangement::Stretch => '2xl:justify-stretch ',
                        HorizontalArrangement::Baseline => '2xl:justify-baseline ',
                        default => '2xl:justify-normal '
                    },
                    default => match ($verticalAlignment) {
                        VerticalAlignment::Center => 'items-center ',
                        VerticalAlignment::End => 'items-end ',
                        default => 'items-start ',
                    } . match ($horizontalArrangement) {
                        HorizontalArrangement::Start => 'justify-start ',
                        HorizontalArrangement::Center => 'justify-center ',
                        HorizontalArrangement::End => 'justify-end ',
                        HorizontalArrangement::Between => 'justify-between ',
                        HorizontalArrangement::Around => 'justify-around ',
                        HorizontalArrangement::Evenly => 'justify-evenly ',
                        HorizontalArrangement::Stretch => 'justify-stretch ',
                        HorizontalArrangement::Baseline => 'justify-baseline ',
                        default => 'justify-normal '
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
                ])
            >
                {{ $component }}
            </div>
        @endforeach
    @endforeach
</div>
