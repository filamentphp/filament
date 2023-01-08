@php
    /**
    * @deprecated Override `logo.blade.php` instead.
    */
@endphp

@if (filled($brand = filament()->getBrandName()))
    <div class="filament-brand text-xl font-bold tracking-tight dark:text-white">
        {{ $brand }}
    </div>
@endif
