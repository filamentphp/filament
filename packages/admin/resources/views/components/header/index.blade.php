@props([
    'actions' => null,
    'heading',
    'icon' => null,
    'iconColor' => null,
    'iconHide' => false,
    'iconPosition' => null,
    'subheading' => null
])

@php
    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'h-7 w-7 shrink-0',
        'text-primary-500' => $iconColor === 'primary',
        'text-danger-500' => $iconColor === 'danger',
        'text-gray-500' => $iconColor === 'secondary',
        'text-success-500' => $iconColor === 'success',
        'text-warning-500' => $iconColor === 'warning',
    ]);
@endphp

<header {{ $attributes->class(['filament-header rounded-xl border border-gray-300 bg-white shadow-sm sm:px-4 md:px-6 lg:px-4 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:rtl:space-x-reverse sm:py-4 dark:border-gray-700 dark:bg-gray-800']) }}>
    <div>
        <div class="inline-flex items-center space-x-1 p-3 rtl:space-x-reverse">
            @if( $icon && ! $iconHide &&  ( ! $iconPosition || $iconPosition == 'left' ) )
                <x-dynamic-component
                    :component="$icon"
                    :class="$iconClasses"
                />
            @endif
                <x-filament::header.heading>
                    {{ $heading }}
                </x-filament::header.heading>
            @if( $icon && ! $iconHide && $iconPosition == 'right' )
                <x-dynamic-component
                    :component="$icon"
                    :class="$iconClasses"
                />
            @endif
        </div>

        @if ($subheading)
            <x-filament::header.subheading class="mt-1">
                {{ $subheading }}
            </x-filament::header.subheading>
        @endif
    </div>

    <x-filament::pages.actions :actions="$actions" class="shrink-0 p-3" />
</header>
