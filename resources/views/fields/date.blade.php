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
        <x-filament::input
            type="date"
            :value="$field->value"
            :id="$field->id"
            :model-directive="$field->modelDirective"
            :model="$field->model"
            :error-key="$field->error ?? $field->model"
            :extra-attributes="$field->extraAttributes"
            x-ref="input"
        />
    </div>
@overwrite