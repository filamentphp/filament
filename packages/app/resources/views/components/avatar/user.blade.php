@props([
    'user' => filament()->auth()->user(),
])

<x-filament::avatar
    :src="filament()->getUserAvatarUrl($user)"
    {{ $attributes }}
/>
