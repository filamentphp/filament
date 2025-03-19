@php
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Enums\HorizontalAlignment;

    $verticalAlignment = $getVerticalAlignment();
    $horizontalAlignment = $getHorizontalAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }

    if (! $horizontalAlignment instanceof HorizontalAlignment) {
        $horizontalAlignment = filled($horizontalAlignment) ? (HorizontalAlignment::tryFrom($horizontalAlignment) ?? $horizontalAlignment) : null;
    }
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-fo-split flex ',
                'gap-' . $getGap() . ' ' => !!$getGap(),
                match ($getFromBreakpoint()) {
                    'sm' => 'flex-col sm:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'sm:items-center ',
                        VerticalAlignment::End => 'sm:items-end ',
                        default => 'sm:items-start ',
                    } . match ($horizontalAlignment) {
                        HorizontalAlignment::Start => 'sm:justify-start ',
                        HorizontalAlignment::Center => 'sm:justify-center ',
                        HorizontalAlignment::End => 'sm:justify-end ',
                        HorizontalAlignment::Between => 'sm:justify-between ',
                        HorizontalAlignment::Around => 'sm:justify-around ',
                        HorizontalAlignment::Evenly => 'sm:justify-evenly ',
                        HorizontalAlignment::Stretch => 'sm:justify-stretch ',
                        HorizontalAlignment::Baseline => 'sm:justify-baseline ',
                        default => 'sm:justify-normal '
                    },
                    'md' => 'flex-col md:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'md:items-center ',
                        VerticalAlignment::End => 'md:items-end ',
                        default => 'md:items-start ',
                    } . match ($horizontalAlignment) {
                        HorizontalAlignment::Start => 'md:justify-start ',
                        HorizontalAlignment::Center => 'md:justify-center ',
                        HorizontalAlignment::End => 'md:justify-end ',
                        HorizontalAlignment::Between => 'md:justify-between ',
                        HorizontalAlignment::Around => 'md:justify-around ',
                        HorizontalAlignment::Evenly => 'md:justify-evenly ',
                        HorizontalAlignment::Stretch => 'md:justify-stretch ',
                        HorizontalAlignment::Baseline => 'md:justify-baseline ',
                        default => 'md:justify-normal '
                    },
                    'lg' => 'flex-col lg:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'lg:items-center ',
                        VerticalAlignment::End => 'lg:items-end ',
                        default => 'lg:items-start ',
                    } . match ($horizontalAlignment) {
                        HorizontalAlignment::Start => 'lg:justify-start ',
                        HorizontalAlignment::Center => 'lg:justify-center ',
                        HorizontalAlignment::End => 'lg:justify-end ',
                        HorizontalAlignment::Between => 'lg:justify-between ',
                        HorizontalAlignment::Around => 'lg:justify-around ',
                        HorizontalAlignment::Evenly => 'lg:justify-evenly ',
                        HorizontalAlignment::Stretch => 'lg:justify-stretch ',
                        HorizontalAlignment::Baseline => 'lg:justify-baseline ',
                        default => 'lg:justify-normal '
                    },
                    'xl' => 'flex-col xl:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => 'xl:items-center ',
                        VerticalAlignment::End => 'xl:items-end ',
                        default => 'xl:items-start ',
                    } . match ($horizontalAlignment) {
                        HorizontalAlignment::Start => 'xl:justify-start ',
                        HorizontalAlignment::Center => 'xl:justify-center ',
                        HorizontalAlignment::End => 'xl:justify-end ',
                        HorizontalAlignment::Between => 'xl:justify-between ',
                        HorizontalAlignment::Around => 'xl:justify-around ',
                        HorizontalAlignment::Evenly => 'xl:justify-evenly ',
                        HorizontalAlignment::Stretch => 'xl:justify-stretch ',
                        HorizontalAlignment::Baseline => 'xl:justify-baseline ',
                        default => 'xl:justify-normal '
                    },
                    '2xl' => 'flex-col 2xl:flex-row ' . match ($verticalAlignment) {
                        VerticalAlignment::Center => '2xl:items-center ',
                        VerticalAlignment::End => '2xl:items-end ',
                        default => '2xl:items-start ',
                    } . match ($horizontalAlignment) {
                        HorizontalAlignment::Start => '2xl:justify-start ',
                        HorizontalAlignment::Center => '2xl:justify-center ',
                        HorizontalAlignment::End => '2xl:justify-end ',
                        HorizontalAlignment::Between => '2xl:justify-between ',
                        HorizontalAlignment::Around => '2xl:justify-around ',
                        HorizontalAlignment::Evenly => '2xl:justify-evenly ',
                        HorizontalAlignment::Stretch => '2xl:justify-stretch ',
                        HorizontalAlignment::Baseline => '2xl:justify-baseline ',
                        default => '2xl:justify-normal '
                    },
                    default => match ($verticalAlignment) {
                        VerticalAlignment::Center => 'items-center ',
                        VerticalAlignment::End => 'items-end ',
                        default => 'items-start ',
                    } . match ($horizontalAlignment) {
                        HorizontalAlignment::Start => 'justify-start ',
                        HorizontalAlignment::Center => 'justify-center ',
                        HorizontalAlignment::End => 'justify-end ',
                        HorizontalAlignment::Between => 'justify-between ',
                        HorizontalAlignment::Around => 'justify-around ',
                        HorizontalAlignment::Evenly => 'justify-evenly ',
                        HorizontalAlignment::Stretch => 'justify-stretch ',
                        HorizontalAlignment::Baseline => 'justify-baseline ',
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
