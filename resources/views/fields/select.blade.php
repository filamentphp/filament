@extends('filament::layouts.field-group')

@section('field')
    <x-filament::select
        :id="$field->id"
        :model-directive="$field->modelDirective"
        :model="$field->model"
        :error-key="$field->error ?? $field->model"
        :extra-attributes="$field->extraAttributes"
    >
        @if ($field->placeholder)
            <option>{{ __($field->placeholder) }}</option>
        @endif

        @foreach ($field->options as $key => $value)
            <option value="{{ $key }}">{{ __($value) }}</option>
        @endforeach
    </x-filament::select>
@overwrite
