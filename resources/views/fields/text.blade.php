@extends('filament::layouts.field-group')

@section('field')
    <x-filament::input
        :disabled="$field->disabled"
        :error-key="$field->errorKey"
        :extra-attributes="$field->attributes"
        :id="$field->id"
        :maxLength="$field->maxLength"
        :minLength="$field->minLength"
        :model-directive="$field->modelDirective"
        :name="$field->name"
        :placeholder="$field->placeholder"
        :required="$field->required"
        :type="$field->type"
    />
@overwrite
