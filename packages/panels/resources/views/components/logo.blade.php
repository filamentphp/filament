@if (
        filled($brand = filament()->getBrandName()) &&
        is_null($logo = filament()->getBrandLogo())
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

@if($logo)
    <img src="{{ $logo }}" 
        loading="lazy"
        alt="{{ $brand }}" 
        class="h-10">
@endif