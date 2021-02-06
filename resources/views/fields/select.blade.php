@extends('filament::layouts.field-group')

@section('field')
    <x-filament::select
        :id="$field->id"
        :model-directive="$field->modelDirective"
        :name="$field->name"
        :error-key="$field->errorKey ?? $field->name"
        :extra-attributes="$field->attributes"
        :required="$field->required"
        :placeholder="$field->placeholder"
        :options="$field->options"
    />
@overwrite
