@extends('filament::layouts.base')

@section('title', $title)

@section('content')
    <main class="flex h-screen items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <header class="text-center space-y-4 mb-6">
                <div class="flex items-center justify-center">
                    <a href="{{ url('/') }}" rel="home" class="inline-flex">
                        <x-filament::app-icon class="w-20 h-20" />
                    </a>
                </div>
                <h2 class="font-light text-2xl md:text-3xl leading-tight">{{ $title ?? config('filament.name') }}</h2>
            </header>
            {{ $slot }}
        </div>
    </main>
@endsection