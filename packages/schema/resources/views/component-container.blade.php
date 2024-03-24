@php
    use Filament\Support\Enums\MaxWidth;
    use Illuminate\Support\Js;

    $isRoot = $isRoot();
@endphp

<x-filament::grid
    :x-data="$isRoot ? '{}' : null"
    :x-on:form-validation-error.window="
        $isRoot ? ('if ($event.detail.livewireId !== ' . Js::from($this->getId()) . ') {
                return
            }

            $nextTick(() => {
                let error = $el.querySelector(\'[data-validation-error]\')

                if (! error) {
                    return
                }

                let elementToExpand = error

                while (elementToExpand) {
                    elementToExpand.dispatchEvent(new CustomEvent(\'expand\'))

                    elementToExpand = elementToExpand.parentNode
                }

                setTimeout(
                    () =>
                        error.closest(\'[data-field-wrapper]\').scrollIntoView({
                            behavior: \'smooth\',
                            block: \'start\',
                            inline: \'start\',
                        }),
                    200,
                )
        })') : null
    "
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
    @foreach ($getComponents(withHidden: true) as $schemaComponent)
        @php
            /**
             * Instead of only rendering the hidden components, we should
             * render the `<div>` wrappers for all fields, regardless of
             * if they are hidden or not. This is to solve Livewire DOM
             * diffing issues.
             *
             * Additionally, any `<div>` elements that wrap hidden
             * components need to have `class="hidden"`, so that they
             * don't consume grid space.
             */
            $isHidden = $schemaComponent->isHidden();
        @endphp

        <x-filament::grid.column
            :wire:key="$schemaComponent instanceof \Filament\Forms\Components\Field ? $this->getId() . '.' . $schemaComponent->getStatePath() . '.' . $schemaComponent::class : null"
            :hidden="$isHidden"
            :default="$schemaComponent->getColumnSpan('default')"
            :sm="$schemaComponent->getColumnSpan('sm')"
            :md="$schemaComponent->getColumnSpan('md')"
            :lg="$schemaComponent->getColumnSpan('lg')"
            :xl="$schemaComponent->getColumnSpan('xl')"
            :twoXl="$schemaComponent->getColumnSpan('2xl')"
            :defaultStart="$schemaComponent->getColumnStart('default')"
            :smStart="$schemaComponent->getColumnStart('sm')"
            :mdStart="$schemaComponent->getColumnStart('md')"
            :lgStart="$schemaComponent->getColumnStart('lg')"
            :xlStart="$schemaComponent->getColumnStart('xl')"
            :twoXlStart="$schemaComponent->getColumnStart('2xl')"
            @class([
                match ($maxWidth = $schemaComponent->getMaxWidth()) {
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
            @if (! $isHidden)
                {{ $schemaComponent }}
            @endif
        </x-filament::grid.column>
    @endforeach
</x-filament::grid>
