@if (filled($brand = filament()->getBrandName()))
    <div
        class="fi-logo text-xl font-bold leading-5 tracking-tight dark:text-white"
    >
        {{ $brand }}
    </div>
@endif
