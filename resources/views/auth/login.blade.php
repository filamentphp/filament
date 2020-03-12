@extends('alpine::layouts.auth')

@section('title', $title)

@section('main')
    <x-alpine-alert :type="session('alert.type')" :message="session('alert.message')" />
    {!! form($form) !!}
@endsection