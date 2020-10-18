@extends('filament::layouts.base')

@section('content')
    <main class="flex h-screen items-center justify-center bg-gray-200 p-4">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>
@endsection