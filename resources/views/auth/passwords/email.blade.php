@extends('alpine::layouts.auth')

@section('title', $title)

@section('main')
    <x-alpine-alert type="success" :message="session('status')" />
    {!! form($form) !!}
@endsection