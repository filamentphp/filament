@extends('filament::layouts.field-group')

@pushonce('head')
    <link rel="stylesheet" href="https://www.unpkg.com/trix@1.3.1/dist/trix.css">
@endpushonce

@pushonce('js')
    <script src="https://www.unpkg.com/trix@1.3.1/dist/trix.js"></script>
@endpushonce

@section('field')
    <div 
        x-data="{ content: @entangle($model).defer }" 
        x-cloak
    >
        <input 
            type="hidden" 
            value="{{ $value }}"
            id="value-{{ $id }}"                 
            @foreach ($extraAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach 
        />
    
        <div 
            wire:ignore
            @trix-change="content = $event.target.value"
            @trix-file-accept="$event.preventDefault()"
        >
            <x-filament::trix-toolbar id="toolbar-{{ $id }}" />

            <trix-editor 
                id="{{ $id }}"
                toolbar="toolbar-{{ $id }}"
                input="value-{{ $id }}" 
                class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 border-gray-300 prose max-w-none"
            ></trix-editor>
        </div>
    </div>
@overwrite
