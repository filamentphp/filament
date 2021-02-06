@extends('filament::layouts.field-group')

@section('field')
    <x-filament::textarea
        :disabled="$field->disabled"
        :error-key="$field->errorKey"
        :extra-attributes="$field->attributes"
        :id="$field->id"
        :model-directive="$field->modelDirective"
        :name="$field->name"
        :placeholder="$field->placeholder"
    />
@overwrite
