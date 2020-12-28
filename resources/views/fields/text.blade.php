@extends('filament::layouts.field-group')

@section('field')
    <input type="{{ $field->type }}" 
        {{ $field->modelDirective }}="{{ $field->model }}"
        value="{{ $field->value }}"
        id="{{ $field->id }}"
        @foreach ($field->extraAttributes as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
        class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($field->error ?? $field->model) border-red-600 motion-safe:animate-shake @else border-gray-300 @enderror"
    />
@overwrite