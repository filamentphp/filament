@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.password.email')">

        <x-filament-alert type="success" :message="session('status')" />

        @include('filament::partials.fields', ['fields' => $fields])

        <x-filament-button label="Send Password Reset Link" class="w-full" />

    </x-filament-form>

@endsection