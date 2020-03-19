@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.login')">

        <x-filament-alert :type="session('alert.type')" :message="session('alert.message')" />
        
        <x-filament-input-group id="email" label="E-Mail Address" required>
            <x-filament-input id="email" type="email" name="email" autocomplete="email" autofocus required />
        </x-filament-input-group>

        <x-filament-input-group id="password" label="Password" required>
            <x-slot name="hint">
                <a href="{{ route('filament.auth.password.forgot') }}">{{ __('Forgot Your Password?') }}</a>
            </x-slot>
            <x-filament-input id="password" type="password" name="password" autocomplete="current-password" required />
        </x-filament-input-group>

        <x-filament-input-group>
            <x-filament-checkbox name="remember" label="Remember Me" />
        </x-filament-input-group>

        <x-filament-button label="Login" class="w-full" />

    </x-filament-form>
    
@endsection