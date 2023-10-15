@props([
    'breadcrumbs' => [],
])

@php
    $iconAlias = 'breadcrumbs.separator';
    $iconClasses = 'h-5 w-5 text-gray-400 dark:text-gray-500';
@endphp

<nav {{ $attributes->class(['fi-breadcrumbs']) }}>
    <ol class="flex flex-wrap items-center gap-x-2">
        @foreach ($breadcrumbs as $url => $label)
            <li class="flex gap-x-2">
                @if (! $loop->first)
                    <x-filament::icon
                        :alias="$iconAlias"
                        icon="heroicon-m-chevron-right"
                        @class([
                            $iconClasses,
                            'rtl:hidden',
                        ])
                    />

                    <x-filament::icon
                        :alias="$iconAlias"
                        icon="heroicon-m-chevron-left"
                        @class([
                            $iconClasses,
                            'ltr:hidden',
                        ])
                    />
                @endif

                <a
                    {{ \Filament\Support\generate_href_html(is_int($url) ? '#' : $url) }}
                    class="text-sm font-medium text-gray-500 outline-none transition duration-75 hover:text-gray-700 focus-visible:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 dark:focus-visible:text-gray-200"
                >
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ol>
</nav>
