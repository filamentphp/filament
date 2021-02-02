@extends('filament::layouts.base')

@section('title', __($title))

@section('content')
    <main class="flex h-screen items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <header class="text-center space-y-4 mb-6">
                <div class="flex items-center justify-center">
                    <x-filament::auth-branding />
                </div>

                <h2 class="font-light text-2xl md:text-3xl leading-tight">{{ __($title) ?? config('app.name') }}</h2>
            </header>

            {{ $slot }}
        </div>
    </main>
@overwrite
