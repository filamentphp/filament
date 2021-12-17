@props([
    'color' => 'primary',
    'detail' => null,
    'icon' => null,
])

<li {{ $attributes }}>
    <button
        type="button"
        @class([
            'flex items-center w-full h-8 px-3 text-sm font-medium focus:outline-none hover:text-white focus:text-white group',
            'hover:bg-primary-600 focus:bg-primary-700' => $color === 'primary',
            'hover:bg-danger-600 focus:bg-danger-700' => $color === 'danger',
            'hover:bg-success-600 focus:bg-success-700' => $color === 'success',
            'hover:bg-warning-600 focus:bg-warning-700' => $color === 'warning',
        ])
    >
        @if ($icon)
            <x-dynamic-component :component="$icon" :class="\Illuminate\Support\Arr::toCssClasses([
                'mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white w-6 h-6',
                'text-primary-500' => $color === 'primary',
                'text-danger-500' => $color === 'danger',
                'text-success-500' => $color === 'success',
                'text-warning-500' => $color === 'warning',
            ])" />
        @endif

        <span>{{ $slot }}</span>

        @if ($detail)
            <span @class([
                'ml-auto text-xs text-gray-500',
                'group-hover:text-primary-100 group-focus:text-primary-100' => $color === 'primary',
                'group-hover:text-danger-100 group-focus:text-danger-100' => $color === 'danger',
                'group-hover:text-success-100 group-focus:text-success-100' => $color === 'success',
                'group-hover:text-warning-100 group-focus:text-warning-100' => $color === 'warning',
            ])>
                {{ $detail }}
            </span>
        @endif
    </button>
</li>
