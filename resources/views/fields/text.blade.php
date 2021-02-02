@extends('filament::layouts.field-group')

@section('field')
    <x-filament::input
        :type="$field->type"
        :id="$field->id"
        :model-directive="$field->modelDirective"
        :name="$field->name"
        :error-key="$field->errorKey ?? $field->name"
        :extra-attributes="$field->extraAttributes"
        :required="$field->required"
        :minLength="$field->minLength"
        :maxLength="$field->maxLength"
    />
@overwrite
