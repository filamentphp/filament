@extends('filament::layouts.base')

@section('title', $title)

@section('content')
    <a href="#content" class="sr-only">Skip to content</a>
    <div class="flex min-h-screen" role="group" tabindex="-1">
        <header role="banner" class="bg-gray-900 text-gray-500 w-56 flex flex-col space-y-4">
            <a href="{{ url('/') }}" rel="home" class="px-4 py-3 flex items-center space-x-4 transition-colors duration-200 hover:text-gray-400 hover:bg-black" target="_blank" rel="noopener noreferrer">
                <x-filament::app-icon class="w-10 h-10 fill-current" />
                <span class="text-sm leading-tight font-semibold">{{ config('app.name') }}</span>
            </a>
            <x-filament::nav />
            <x-filament::dropdown class="flex-grow flex items-center space-x-3 p-4" id="dropdown-user">
                <x-slot name="content">
                    <ul class="list-dropdown" aria-label="{{ __('User actions') }}">
                        <li><a href="#">{{ __('Edit Profile') }}</a></li>
                        <li><livewire:filament-logout /></li>
                    </ul>
                </x-slot>

                <img src="{{ Auth::user()->avatar(32) }}" alt="{{ Auth::user()->name }}" srcset="{{ Auth::user()->avatar(32) }} 1x, {{ Auth::user()->avatar(64) }} 2x" class="flex-shrink-0 w-8 h-8 rounded" />
                <span class="flex-grow text-sm leading-tight font-semibold">{{ Auth::user()->name }}</span>
            </x-filament::dropdown>
        </header>
        <div class="flex-grow flex flex-col">
            <header class="p-6 flex justify-between items-center space-x-4">
                <h1 class="font-light text-2xl leading-tight text-orange-700">{{ $title }}</h1>
                {{ $actions ?? null }}
            </header>
            <main id="content" class="flex-grow flex flex-col">
                {{ $slot }}
            </main>
            <footer role="contentinfo" class="p-6 flex items-center justify-between">
                <span class="text-xs font-mono text-gray-500">{{ config('filament.name') }}</span>
            </footer>
        </div>
    </div>
@endsection