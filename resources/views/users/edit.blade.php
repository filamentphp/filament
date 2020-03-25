@extends('filament::layouts.admin')

@section('title', $title)

@section('main')

    @livewire('filament::user-edit', [
        'model' => $user, 
        'goback' => 'alpine.admin.users.index'
    ])

@endsection