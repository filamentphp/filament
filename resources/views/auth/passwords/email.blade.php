@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.password.email')">

        <x-filament-alert type="success" :message="session('status')" />

        <x-filament-fields :fields="$fields" />

        <button type="submit" class="btn w-full">{{ __('Send Password Reset Link') }}</button>

    </x-filament-form>

@endsection