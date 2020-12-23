@extends('filament::layouts.field-group')

@pushonce('head')
    <link rel="stylesheet" href="https://www.unpkg.com/trix@1.3.1/dist/trix.css">
@endpushonce

@pushonce('js')
    <script src="https://www.unpkg.com/trix@1.3.1/dist/trix.js"></script>
@endpushonce

@section('field')
    <div 
        x-data="{ content: @entangle($field->model).defer }" 
        x-cloak
    >
        <input 
            type="hidden" 
            value="{{ $field->value }}"
            id="value-{{ $field->id }}"                 
        />
    
        <div 
            wire:ignore
            @trix-change="content = $event.target.value"
            @trix-file-accept="$event.preventDefault()"
        >
            <x-filament::trix-toolbar id="toolbar-{{ $field->id }}" />

            <trix-editor 
                id="{{ $field->id }}"
                toolbar="toolbar-{{ $field->id }}"
                input="value-{{ $field->id }}" 
                class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-white border-gray-300 prose max-w-none"
                @foreach ($field->extraAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach 
            ></trix-editor>
        </div>
    </div>
@overwrite
