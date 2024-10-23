@php
    use Illuminate\View\ComponentAttributeBag;

    use function Filament\Support\generate_icon_html;
@endphp

@props([
    'breadcrumbs' => [],
])

@php
    $iconClasses = 'fi-breadcrumbs-item-separator flex size-5 text-gray-400 dark:text-gray-500';
    $itemLabelClasses = 'fi-breadcrumbs-item-label text-sm font-medium text-gray-500 dark:text-gray-400';
@endphp

<nav {{ $attributes->class(['fi-breadcrumbs']) }}>
    <ol class="fi-breadcrumbs-list flex flex-wrap items-center gap-x-2">
        @foreach ($breadcrumbs as $url => $label)
            <li class="fi-breadcrumbs-item flex items-center gap-x-2">
                @if (! $loop->first)
                    {{
                        generate_icon_html('heroicon-m-chevron-right', alias: 'breadcrumbs.separator', attributes: (new ComponentAttributeBag)->class([
                            $iconClasses,
                            'rtl:hidden',
                        ]))
                    }}

                    {{
                        generate_icon_html('heroicon-m-chevron-left', alias: 'breadcrubs.separator.rtl', attributes: (new ComponentAttributeBag)->class([
                            $iconClasses,
                            'ltr:hidden',
                        ]))
                    }}
                @endif

                @if (is_int($url))
                    <span class="{{ $itemLabelClasses }}">
                        {{ $label }}
                    </span>
                @else
                    <a
                        {{ \Filament\Support\generate_href_html($url) }}
                        class="{{ $itemLabelClasses }} transition duration-75 hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        {{ $label }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
