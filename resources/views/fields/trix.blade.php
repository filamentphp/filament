@extends('filament::layouts.field-group')

@pushonce('head:trix')
    @style('https://www.unpkg.com/trix@1.3.1/dist/trix.css')
@endpushonce

@pushonce('js:trix')
    @script('https://www.unpkg.com/trix@1.3.1/dist/trix.js')
@endpushonce

@section('field')
    <div 
        wire:ignore
        @if ($model)
            {{ $modelDirective }}="{{ $model }}"
        @endif
        x-data
        @trix-blur="$dispatch('change', $event.target.value)"
    >
        <input type="hidden" id="value-{{ $id ?? $name }}" value="{{ $value ?? '' }}" />

        <trix-editor 
            id="{{ $id ?? $name }}"
            input="value-{{ $id ?? $name }}" 
            class="block w-full rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 border-gray-300"
            @if ($extraAttributes)
                @foreach ($extraAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach
            @endif
        ></trix-editor>
    </div>
@overwrite
