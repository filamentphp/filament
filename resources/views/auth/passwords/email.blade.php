@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.password.email')">

        <x-filament-alert type="success" :message="session('status')" />

        <x-filament-input-group id="email" label="E-Mail Address" required>
            <x-filament-input id="email" type="email" name="email" autocomplete="email" autofocus required />
        </x-filament-input-group>

        <x-filament-button label="Send Password Reset Link" class="w-full" />

        <x-slot name="hint">
            <a href="{{ route('filament.auth.login') }}">&larr; {{ __('Back to Login') }}</a>
        </x-slot>

    </x-filament-form>

@endsection