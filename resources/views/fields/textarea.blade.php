@extends('filament::layouts.field-group')

@section('field')
    <x-filament::textarea
        :id="$field->id"
        :model-directive="$field->modelDirective"
        :model="$field->model"
        :error-key="$field->error ?? $field->model"
        :extra-attributes="$field->extraAttributes"
    >{{ $field->value }}</x-filament::textarea>
@overwrite