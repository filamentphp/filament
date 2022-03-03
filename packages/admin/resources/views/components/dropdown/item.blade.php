@props([
    'color' => 'primary',
    'detail' => null,
    'icon' => null,
    'tag' => 'button',
    'type' => 'button',
])

@php
    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        'flex items-center w-full h-8 px-3 text-sm font-medium focus:outline-none hover:text-white focus:text-white group whitespace-nowrap',
        'hover:bg-primary-600 focus:bg-primary-700' => $color === 'primary' || $color === 'secondary',
        'hover:bg-danger-600 focus:bg-danger-700' => $color === 'danger',
        'hover:bg-success-600 focus:bg-success-700' => $color === 'success',
        'hover:bg-warning-600 focus:bg-warning-700' => $color === 'warning',
    ]);

    $detailClasses = \Illuminate\Support\Arr::toCssClasses([
        'ml-auto text-xs text-gray-500',
        'group-hover:text-primary-100 group-focus:text-primary-100' => $color === 'primary' || $color === 'secondary',
        'group-hover:text-danger-100 group-focus:text-danger-100' => $color === 'danger',
        'group-hover:text-success-100 group-focus:text-success-100' => $color === 'success',
        'group-hover:text-warning-100 group-focus:text-warning-100' => $color === 'warning',
    ]);

    $labelClasses = 'truncate';

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'mr-2 -ml-1 w-6 h-6 flex-shrink-0 rtl:ml-2 rtl:-mr-1 rtl:scale-x-[-1] group-hover:text-white group-focus:text-white',
        'text-primary-500' => $color === 'primary',
        'text-danger-500' => $color === 'danger',
        'text-gray-500' => $color === 'secondary',
        'text-success-500' => $color === 'success',
        'text-warning-500' => $color === 'warning',
    ]);
@endphp

<li {{ $attributes->only(['class']) }}>
    @if ($tag === 'button')
        <button
            type="{{ $type }}"
            {{ $attributes->except(['class'])->class([$buttonClasses]) }}
        >
            @if ($icon)
                <x-dynamic-component :component="$icon" :class="$iconClasses" />
            @endif

            <span class="{{ $labelClasses }}">
                {{ $slot }}
            </span>

            @if ($detail)
                <span class="{{ $detailClasses }}">
                    {{ $detail }}
                </span>
            @endif
        </button>
    @elseif ($tag === 'a')
        <a {{ $attributes->except(['class'])->class([$buttonClasses]) }}>
            @if ($icon)
                <x-dynamic-component :component="$icon" :class="$iconClasses" />
            @endif

            <span class="{{ $labelClasses }}">
                {{ $slot }}
            </span>

            @if ($detail)
                <span class="{{ $detailClasses }}">
                    {{ $detail }}
                </span>
            @endif
        </a>
    @endif
</li>
