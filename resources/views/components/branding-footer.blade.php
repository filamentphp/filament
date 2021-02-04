<a 
    href="{{ config('filament.url') }}" 
    target="_blank" 
    rel="noopener noreferrer" 
    {{ $attributes->merge(['class' => 'inline-flex items-center opacity-20 hover:opacity-100']) }}
>
    <x-filament::logo class="w-28 h-auto" />
</a>
