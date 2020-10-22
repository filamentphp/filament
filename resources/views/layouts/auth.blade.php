@extends('filament::layouts.base')

@section('title', $title ?? null)

@section('content')
    <main class="flex h-screen items-center justify-center bg-gradient-to-b from-gray-200 to-gray-400 p-4">
        <div class="w-full max-w-sm">
            <header class="text-center space-y-2 mb-6">
                <div class="flex items-center justify-center">
                    <a href="{{ url('/') }}" rel="home" class="inline-flex text-green-500 hover:text-green-600 transition-colors duration-200">
                        <x-filament::logo />
                    </a>
                </div>
                <h2 class="font-bold text-2xl md:text-3xl leading-tight">{{ $title ?? config('app.name', 'Laravel') }}</h2>
            </header>
            {{ $slot }}
        </div>
    </main>
@endsection