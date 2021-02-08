<a 
    href="{{ config('filament.url') }}" 
    target="_blank" 
    rel="noopener noreferrer" 
    {{ $attributes->merge(['class' => 'inline-flex items-center opacity-20 hover:opacity-100 transition-opacity duration-500']) }}
>
    <x-filament::logo class="w-24 h-auto" />
</a>
