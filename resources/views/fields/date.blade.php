@extends('filament::layouts.field-group')

@pushonce('head:flatpickr-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpushonce

@pushonce('js:flatpickr')
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
            :name="$field->name"
            :error-key="$field->errorKey"
            :extra-attributes="$field->attributes"
            x-ref="input"
        />
    </div>
@overwrite
