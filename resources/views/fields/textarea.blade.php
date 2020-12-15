@extends('filament::layouts.field-group')

@section('field')
    <textarea 
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        @if ($model)
            {{ $modelDirective }}="{{ $model }}"
        @endif
        @if ($extraAttributes)
            @foreach ($extraAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach
        @endif
        class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error($model ?? $name) border-red-600 motion-safe:animate-shake @else border-gray-300 @enderror">
        {{ $value ?? '' }}
    </textarea>
@overwrite