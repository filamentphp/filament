@extends('alpine::layouts.auth')

@section('title', $title)

@section('main')

    <x-alpine-form :action="route('alpine.auth.password.email')">

        <x-alpine-alert type="success" :message="session('status')" />

        <x-alpine-input-group id="email" label="E-Mail Address" required>
            <x-alpine-input id="email" type="email" name="email" autocomplete="email" autofocus required />
        </x-alpine-input-group>

        <x-alpine-button label="Send Password Reset Link" class="w-full" />

        <x-slot name="hint">
            <a href="{{ route('alpine.auth.login') }}">&larr; {{ __('Back to Login') }}</a>
        </x-slot>

    </x-alpine-form>

@endsection