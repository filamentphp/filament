@extends('filament::layouts.field-group')

@section('field')
    <select
        {{ $modelDirective }}="{{ $model }}"
        id="{{ $id }}"
        @foreach ($extraAttributes as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
        class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($model) border-red-600 motion-safe:animate-shake @else border-gray-300 @enderror"
    >
        @if ($placeholder)
            <option>{{ __($placeholder) }}</option>
        @endif
        @foreach ($options as $key => $value)
            <option value="{{ $key }}">{{ __($value) }}</option>
        @endforeach
    </select>
@overwrite