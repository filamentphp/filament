@extends('filament::layouts.admin')

@section('title', $title)

@section('main')
    @livewire('filament::user-edit-form', ['user' => $user])
@endsection