@extends('filament::layouts.base')

@section('content')
    <main class="flex h-screen items-center justify-center bg-gray-200">
        <div class="w-full max-w-md m-4 p-4 bg-white rounded shadow-md">
            {{ $slot }}
        </div>
    </main>
@endsection