@php
    use Filament\Support\Enums\MaxWidth;
@endphp

<dl>
    <x-filament::grid
        :default="$getColumns('default')"
        :sm="$getColumns('sm')"
        :md="$getColumns('md')"
        :lg="$getColumns('lg')"
        :xl="$getColumns('xl')"
        :two-xl="$getColumns('2xl')"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['fi-fo-component-ctn gap-6'])
        "
    >
        @foreach ($getComponents() as $infolistComponent)
            <x-filament::grid.column
                :default="$infolistComponent->getColumnSpan('default')"
                :sm="$infolistComponent->getColumnSpan('sm')"
                :md="$infolistComponent->getColumnSpan('md')"
                :lg="$infolistComponent->getColumnSpan('lg')"
                :xl="$infolistComponent->getColumnSpan('xl')"
                :twoXl="$infolistComponent->getColumnSpan('2xl')"
                :defaultStart="$infolistComponent->getColumnStart('default')"
                :smStart="$infolistComponent->getColumnStart('sm')"
                :mdStart="$infolistComponent->getColumnStart('md')"
                :lgStart="$infolistComponent->getColumnStart('lg')"
                :xlStart="$infolistComponent->getColumnStart('xl')"
                :twoXlStart="$infolistComponent->getColumnStart('2xl')"
                @class([
                    match ($maxWidth = $infolistComponent->getMaxWidth()) {
                        MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
                        MaxWidth::Small, 'sm' => 'max-w-sm',
                        MaxWidth::Medium, 'md' => 'max-w-md',
                        MaxWidth::Large, 'lg' => 'max-w-lg',
                        MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
                        MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
                        MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
                        MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
                        MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
                        MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
                        MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
                        default => $maxWidth,
                    },
                ])
            >
                {{ $infolistComponent }}
            </x-filament::grid.column>
        @endforeach
    </x-filament::grid>
</dl>
