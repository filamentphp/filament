@php
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;

    use function Filament\Support\generate_icon_html;
@endphp

@props([
    'breadcrumbs' => [],
])

<nav
    {{
        $attributes
            ->merge(['aria-label' => __('filament::components/breadcrumbs.label')], escape: true)
            ->class(['fi-breadcrumbs'])
    }}
>
    <ol class="fi-breadcrumbs-list">
        @foreach ($breadcrumbs as $url => $label)
            <li class="fi-breadcrumbs-item">
                @if (! $loop->first)
                    {{
                        generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronRight, alias: \Filament\Support\View\SupportIconAlias::BREADCRUMBS_SEPARATOR, attributes: (new FilamentComponentAttributeBag)->merge(['aria-hidden' => 'true'], escape: false)->class([
                            'fi-breadcrumbs-item-separator fi-ltr',
                        ]))
                    }}

                    {{
                        generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronLeft, alias: \Filament\Support\View\SupportIconAlias::BREADCRUMBS_SEPARATOR_RTL, attributes: (new FilamentComponentAttributeBag)->merge(['aria-hidden' => 'true'], escape: false)->class([
                            'fi-breadcrumbs-item-separator fi-rtl',
                        ]))
                    }}
                @endif

                @if (is_int($url))
                    <span
                        @if ($loop->last) aria-current="page" @endif
                        class="fi-breadcrumbs-item-label"
                    >
                        {{ $label }}
                    </span>
                @else
                    <a
                        {{ \Filament\Support\generate_href_html($url) }}
                        @if ($loop->last) aria-current="page" @endif
                        class="fi-breadcrumbs-item-label"
                    >
                        {{ $label }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
