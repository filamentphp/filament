@extends('alpine::layouts.admin')

@section('title', $title)

@section('main')
    @livewire('alpine::users')
@endsection