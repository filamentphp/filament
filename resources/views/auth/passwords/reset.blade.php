@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.password.update')">

        <input type="hidden" name="token" value="{{ $token }}" required />

        @include('filament::partials.fields', ['fields' => $fields])

        <x-filament-button label="Reset Password" class="w-full" />

        <x-slot name="hint">
            <a href="{{ route('filament.auth.login') }}">&larr; {{ __('Back to Login') }}</a>
        </x-slot>

    </x-filament-form>

@endsection