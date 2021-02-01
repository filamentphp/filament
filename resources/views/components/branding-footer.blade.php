<a 
    href="{{ config('filament.url') }}" 
    target="_blank" 
    rel="noopener noreferrer" 
    {{ $attributes->merge(['class' => 'inline-flex items-center transition-colors duration-200 text-gray-400 hover:text-gray-600']) }}
>
    <x-filament::app-icon class="w-10 h-10" />
    <span class="text-sm font-mono">{{ config('filament.name') }}</span>
</a>
