@if (
        filled($brand = filament()->getBrandName()) &&
        is_null(filament()->getBrandLogo())
    )
    <div
        {{
            $attributes->class([
                'fi-logo text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white',
            ])
        }}
    >
        {{ $brand }}
    </div>
@endif

@if(filled($logoPath = filament()->getBrandLogo()))
    <img src="{{ $logoPath }}" 
        loading="lazy"
        alt="{{ filament()->getBrandName() }}" 
        class="h-10">
@endif