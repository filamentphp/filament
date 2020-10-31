@extends('filament::layouts.base')

@section('title', $title)

@section('content')
    <div class="flex min-h-screen" role="group" tabindex="-1">
        <nav aria-label="Main" class="bg-gray-900 w-64">
            <a href="#content" class="sr-only">Skip to content</a>
        </nav>
        <div class="flex-grow flex flex-col">
            <header role="banner" class="p-4 bg-gray-100 border-b border-gray-500 flex justify-between items-center">
                <h1>{{ $title }}</h1>
                <livewire:filament-logout />
            </header>
            <main id="content" class="flex-grow flex flex-col">
                {{ $slot }}
            </main>
            <footer role="contentinfo" class="p-4 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-700">{{ config('filament.name') }}</span>
            </footer>
        </div>
    </div>
@endsection