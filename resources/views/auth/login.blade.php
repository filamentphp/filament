@extends('alpine::layouts.auth')

@section('title', $title)

@section('main')
    
    <x-alpine-alert :type="session('alert.type')" :message="session('alert.message')" />

    <x-alpine-form :action="route('alpine.auth.login')">

        <x-alpine-input-group id="email" label="E-Mail Address" required>
            <x-alpine-input id="email" type="email" name="email" autocomplete="email" autofocus required />
        </x-alpine-input-group>

        <x-alpine-input-group id="password" label="Password" required>
            <x-slot name="hint">
                <a href="{{ route('alpine.auth.password.forgot') }}">{{ __('Forgot Your Password?') }}</a>
            </x-slot>
            <x-alpine-input id="password" type="password" name="password" autocomplete="current-password" required />
        </x-alpine-input-group>

        <x-alpine-input-group>
            <x-alpine-checkbox name="remember" label="Remember Me" />
        </x-alpine-input-group>

        <x-alpine-button label="Login" />

    </x-alpine-form>
    
@endsection