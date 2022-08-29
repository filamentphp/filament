<x-filament-support::grid
    :default="$getColumns('default')"
    :sm="$getColumns('sm')"
    :md="$getColumns('md')"
    :lg="$getColumns('lg')"
    :xl="$getColumns('xl')"
    :two-xl="$getColumns('2xl')"
    class="filament-forms-component-container gap-6"
>
    @foreach ($getComponents(withHidden: true) as $formComponent)
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
            $isVisible = ! $formComponent->isHidden();
        @endphp

        <div
            @if ($formComponent instanceof \Filament\Forms\Components\Field)
                wire:key="{{ $this->id }}.{{ $formComponent->getStatePath() }}.{{ $formComponent::class }}"
            @endif
            @if ($isVisible)
                @class([
                    ($maxWidth = $formComponent->getMaxWidth()) ? match ($maxWidth) {
                        'xs' => 'max-w-xs',
                        'sm' => 'max-w-sm',
                        'md' => 'max-w-md',
                        'lg' => 'max-w-lg',
                        'xl' => 'max-w-xl',
                        '2xl' => 'max-w-2xl',
                        '3xl' => 'max-w-3xl',
                        '4xl' => 'max-w-4xl',
                        '5xl' => 'max-w-5xl',
                        '6xl' => 'max-w-6xl',
                        '7xl' => 'max-w-7xl',
                        default => $maxWidth,
                    } : null,
                    ($defaultSpan = $formComponent->getColumnSpan('default')) ? match ($defaultSpan) {
                        1 => 'col-span-1',
                        2 => 'col-span-2',
                        3 => 'col-span-3',
                        4 => 'col-span-4',
                        5 => 'col-span-5',
                        6 => 'col-span-6',
                        7 => 'col-span-7',
                        8 => 'col-span-8',
                        9 => 'col-span-9',
                        10 => 'col-span-10',
                        11 => 'col-span-11',
                        12 => 'col-span-12',
                        'full' => 'col-span-full',
                        default => $defaultSpan,
                    } : null,
                    ($smSpan = $formComponent->getColumnSpan('sm')) ? match ($smSpan) {
                        1 => 'sm:col-span-1',
                        2 => 'sm:col-span-2',
                        3 => 'sm:col-span-3',
                        4 => 'sm:col-span-4',
                        5 => 'sm:col-span-5',
                        6 => 'sm:col-span-6',
                        7 => 'sm:col-span-7',
                        8 => 'sm:col-span-8',
                        9 => 'sm:col-span-9',
                        10 => 'sm:col-span-10',
                        11 => 'sm:col-span-11',
                        12 => 'sm:col-span-12',
                        'full' => 'sm:col-span-full',
                        default => $smSpan,
                    } : null,
                    ($mdSpan = $formComponent->getColumnSpan('md')) ? match ($mdSpan) {
                        1 => 'md:col-span-1',
                        2 => 'md:col-span-2',
                        3 => 'md:col-span-3',
                        4 => 'md:col-span-4',
                        5 => 'md:col-span-5',
                        6 => 'md:col-span-6',
                        7 => 'md:col-span-7',
                        8 => 'md:col-span-8',
                        9 => 'md:col-span-9',
                        10 => 'md:col-span-10',
                        11 => 'md:col-span-11',
                        12 => 'md:col-span-12',
                        'full' => 'md:col-span-full',
                        default => $mdSpan,
                    } : null,
                    ($lgSpan = $formComponent->getColumnSpan('lg')) ? match ($lgSpan) {
                        1 => 'lg:col-span-1',
                        2 => 'lg:col-span-2',
                        3 => 'lg:col-span-3',
                        4 => 'lg:col-span-4',
                        5 => 'lg:col-span-5',
                        6 => 'lg:col-span-6',
                        7 => 'lg:col-span-7',
                        8 => 'lg:col-span-8',
                        9 => 'lg:col-span-9',
                        10 => 'lg:col-span-10',
                        11 => 'lg:col-span-11',
                        12 => 'lg:col-span-12',
                        'full' => 'lg:col-span-full',
                        default => $lgSpan,
                    } : null,
                    ($xlSpan = $formComponent->getColumnSpan('xl')) ? match ($xlSpan) {
                        1 => 'xl:col-span-1',
                        2 => 'xl:col-span-2',
                        3 => 'xl:col-span-3',
                        4 => 'xl:col-span-4',
                        5 => 'xl:col-span-5',
                        6 => 'xl:col-span-6',
                        7 => 'xl:col-span-7',
                        8 => 'xl:col-span-8',
                        9 => 'xl:col-span-9',
                        10 => 'xl:col-span-10',
                        11 => 'xl:col-span-11',
                        12 => 'xl:col-span-12',
                        'full' => 'xl:col-span-full',
                        default => $xlSpan,
                    } : null,
                    ($twoXlSpan = $formComponent->getColumnSpan('2xl')) ? match ($twoXlSpan) {
                        1 => '2xl:col-span-1',
                        2 => '2xl:col-span-2',
                        3 => '2xl:col-span-3',
                        4 => '2xl:col-span-4',
                        5 => '2xl:col-span-5',
                        6 => '2xl:col-span-6',
                        7 => '2xl:col-span-7',
                        8 => '2xl:col-span-8',
                        9 => '2xl:col-span-9',
                        10 => '2xl:col-span-10',
                        11 => '2xl:col-span-11',
                        12 => '2xl:col-span-12',
                        'full' => '2xl:col-span-full',
                        default => $twoXlSpan,
                    } : null,
                ])
            @else
                class="hidden"
            @endif
        >
            @if ($isVisible)
                {{ $formComponent }}
            @endif
        </div>
    @endforeach
</x-filament-support::grid>
