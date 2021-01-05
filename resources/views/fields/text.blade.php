@extends('filament::layouts.field-group')

@section('field')
    <x-filament::input
        :type="$field->type"
        :value="$field->value"
        :id="$field->id"
        :model-directive="$field->modelDirective"
        :model="$field->model"
        :error-key="$field->error ?? $field->model"
        :extra-attributes="$field->extraAttributes"
    />
@overwrite