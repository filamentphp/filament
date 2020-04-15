@extends('filament::layouts.app', ['classes' => ['body' => 'min-h-screen bg-gray-100 dark:bg-gray-800 flex flex-col justify-center px-4 py-12 sm:px-6 lg:px-8']])

@section('content')
    <main id="content" class="sm:mx-auto sm:w-full sm:max-w-md" tabindex="0">
        <x-filament-logo class="w-16 mx-auto" />
        <h2 class="my-6 text-center text-3xl leading-9 font-extrabold">
            @yield('title')
        </h2>
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            @yield('main')
            <div class="mt-6 flex justify-center">
                @include('filament::partials.dark-mode-toggle')
            </div>
        </div>
    </main>
@endsection