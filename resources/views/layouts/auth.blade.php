@extends('filament::layouts.app', ['classes' => ['body' => 'min-h-screen bg-gray-100 dark:bg-gray-800 flex flex-col justify-center px-4 py-12 sm:px-6 lg:px-8']])

@section('content')
    <main id="content" class="sm:mx-auto sm:w-full sm:max-w-md" tabindex="0">
        @include('filament::partials.logo', ['classes' => 'mx-auto w-16 h-16 rounded-full'])
        <h2 class="mt-4 text-center text-3xl leading-9 font-extrabold">
            @yield('title')
        </h2>
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm">
            @yield('main')
        </div>
    </main>
@endsection