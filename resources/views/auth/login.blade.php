@extends('filament::layouts.auth')

@section('title', $title)

@section('main')

    <x-filament-form :action="route('filament.auth.login')">

        <x-filament-alert :type="session('alert.type')" :message="session('alert.message')" />
        
        <x-filament-fields :fields="$fields" />

        <x-filament-button label="Login" class="w-full" />

    </x-filament-form>
    
@endsection