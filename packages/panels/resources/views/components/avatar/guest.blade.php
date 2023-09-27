@props([
    'size' => 'md',
])

<div 
    class="flex items-center justify-center bg-gray-950 rounded-full
    {{ 
        match ($size) {
            'md' => 'h-9 w-9',
            'lg' => 'h-10 w-10',
            default => $size,
        }
    }}"
>
    <x-filament::icon
        icon="heroicon-o-lock-closed"
        class="h-6 w-6 m-0 text-white"
    />
</div>