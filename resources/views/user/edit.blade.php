@extends('alpine::layouts.admin')

@section('title', $title)

@section('main')
    @livewire('alpine::user-edit-form', ['user' => $user])
@endsection