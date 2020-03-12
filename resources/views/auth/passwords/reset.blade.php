@extends('alpine::layouts.auth')

@section('title', $title)

@section('main')

    <x-alpine-form :action="route('alpine.auth.password.update')">

        <input type="hidden" name="token" value="{{ $token }}" required />

        <x-alpine-input-group id="email" label="E-Mail Address" required>
            <x-alpine-input id="email" type="email" name="email" :value="$email" autocomplete="email" autofocus required />
        </x-alpine-input-group>

        <x-alpine-input-group id="password" label="Password" required>
            <x-alpine-input id="password" type="password" name="password" autocomplete="new-password" required />
        </x-alpine-input-group>

        <x-alpine-input-group id="password-confirm" label="Confirm Password" required>
            <x-alpine-input id="password-confirm" type="password" name="password_confirmation" autocomplete="new-password" required />
        </x-alpine-input-group>

        <x-alpine-button label="Reset Password" />

    </x-alpine-form>

@endsection