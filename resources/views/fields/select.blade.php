@extends('filament::layouts.field-group')

@section('field')
    <select
        {{ $field->modelDirective }}="{{ $field->model }}"
        id="{{ $field->id }}"
        @foreach ($field->extraAttributes as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
        class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($field->model) border-red-600 motion-safe:animate-shake @else border-gray-300 @enderror"
    >
        @if ($field->placeholder)
            <option>{{ __($field->placeholder) }}</option>
        @endif
        @foreach ($field->options as $key => $value)
            <option value="{{ $key }}">{{ __($value) }}</option>
        @endforeach
    </select>
@overwrite