@extends('filament::layouts.field-group')

@section('field')
    <input type="{{ $type }}" 
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        value="{{ $value ?? '' }}"
        @if ($model)
            {{ $modelDirective }}="{{ $model }}"
        @endif
        @if ($extraAttributes)
            @foreach ($extraAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach
        @endif
        class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($model ?? $name) border-red-600 motion-safe:animate-shake @else border-gray-300 @enderror"
    />
@overwrite