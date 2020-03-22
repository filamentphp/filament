@extends('filament::layouts.admin')

@section('title', $title)

@section('main')
    @livewire('filament::user-edit', ['user' => $user])
@endsection