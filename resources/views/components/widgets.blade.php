<section 
    aria-label="{{ __('filament::widgets.title') }}" 
    {{ $attributes->merge(['class' => 'grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 xl:gap-8']) }}
>
    {{ $slot }}
</section>