@extends('filament::layouts.base')

@section('title', $title ?? null)

@section('content')
    <main class="flex h-screen items-center justify-center bg-gray-200 p-4">
        <div class="w-full max-w-md">
            <header class="text-center space-y-2 mb-6">
                <div class="flex items-center justify-center">
                    <a href="{{ url('/') }}" rel="home" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <x-filament::logo class="w-12 h-12 fill-current" />
                    </a>
                </div>
                <h2 class="font-black text-2xl md:text-3xl leading-tight">{{ $title ?? config('app.name', 'Laravel') }}</h2>
            </header>
            {{ $slot }}
        </div>
    </main>
@endsection