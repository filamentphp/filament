@php
    $brandName = filament()->getBrandName();
    $brandLogo = filament()->getBrandLogo();
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.25rem';
@endphp

@if ($brandLogo instanceof \Illuminate\Contracts\Support\Htmlable)
    <div
        {{
            $attributes
                ->class(['fi-logo'])
                ->style([
                    "height: {$brandLogoHeight}",
                ])
        }}
    >
        {{ $brandLogo }}
    </div>
@elseif (filled($brandLogo))
    <img
        alt="{{ $brandName }}"
        loading="lazy"
        src="{{ $brandLogo }}"
        {{
            $attributes
                ->class(['fi-logo'])
                ->style([
                    "height: {$brandLogoHeight}",
                ])
        }}
    />
@else
    <div
        {{ $attributes->class(['fi-logo text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white']) }}
    >
        {{ $brandName }}
    </div>
@endif
