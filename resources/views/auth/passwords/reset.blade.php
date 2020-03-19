@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.password.update')">

        <input type="hidden" name="token" value="{{ $token }}" required />

        <x-filament-input-group id="email" label="E-Mail Address" required>
            <x-filament-input id="email" type="email" name="email" :value="$email" autocomplete="email" autofocus required />
        </x-filament-input-group>

        <x-filament-input-group id="password" label="Password" required>
            <x-filament-input id="password" type="password" name="password" autocomplete="new-password" required />
        </x-filament-input-group>

        <x-filament-input-group id="password-confirm" label="Confirm Password" required>
            <x-filament-input id="password-confirm" type="password" name="password_confirmation" autocomplete="new-password" required />
        </x-filament-input-group>

        <x-filament-button label="Reset Password" class="w-full" />

        <x-slot name="hint">
            <a href="{{ route('filament.auth.login') }}">&larr; {{ __('Back to Login') }}</a>
        </x-slot>

    </x-filament-form>

@endsection