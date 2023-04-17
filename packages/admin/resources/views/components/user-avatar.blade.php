@props([
    'user' => \Filament\Facades\Filament::auth()->user(),
])

<div
    {{ $attributes->class([
        'w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center',
        'dark:bg-gray-900' => config('filament.dark_mode'),
    ]) }}
    style="background-image: url('{{ \Filament\Facades\Filament::getUserAvatarUrl($user) }}')"
></div>
