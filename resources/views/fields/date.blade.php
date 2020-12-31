@extends('filament::layouts.field-group')

@pushonce('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpushonce

@pushonce('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpushonce

@section('field')
    <div 
        wire:ignore 
        x-data 
        x-init='flatpickr($refs.input, @json($field->config))'
    >
        <input type="date" 
            {{ $field->modelDirective }}="{{ $field->model }}"
            value="{{ $field->value }}"
            id="{{ $field->id }}"
            x-ref="input"
            @foreach ($field->extraAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach
            class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($field->error ?? $field->model) border-red-600 motion-safe:animate-shake @else border-gray-300 @enderror"
        />
    </div>
@overwrite