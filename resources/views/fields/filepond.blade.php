@extends('filament::layouts.field-group')

@pushonce('head')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.css">
@endpushonce

@pushonce('js')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endpushonce

@section('field')
    <div class="p-4 rounded focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 border @error($field->error ?? $field->model) border-red-600 motion-safe:animate-shake @else border-gray-100 @enderror space-y-6">
        <div 
            x-data 
            x-init="
                FilePond.setOptions({
                    allowMultiple: {{ isset($field->extraAttributes['multiple']) ? 'true' : 'false' }},
                    server: {
                        process:(fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                            @this.upload('{{ $field->model }}', file, load, error, progress)
                        },
                        revert: (filename, load, error) => {
                            @this.removeUpload('{{ $field->model }}', filename, load)
                        },
                    },
                });
                FilePond.create($refs.input);
            " 
            wire:ignore
        >
            <input 
                type="file" 
                x-ref="input"
            />
        </div>
        @if ($field->value)
            <div class="overflow-hidden">
                <ol class="flex flex-wrap -m-2">
                    @foreach ($field->value as $file)
                        <li class="w-full md:w-auto p-2">
                            <div class="shadow-sm rounded border border-gray-300 p-2 flex items-center space-x-2">
                                @if (Filament::isImage($file))
                                    <x-filament-image :src="$file" alt="{{ $file }}" :manipulations="[ 'w' => 32, 'h' => 32, 'fit' => 'crop' ]" class="flex-shrink-0 w-8 h-8 rounded" />    
                                @endif
                                <a href="{{ Filament::url($file) }}" target="_blank" rel="noopener noreferrer" class="font-mono text-xs leading-tight text-gray-500">
                                    {{ $file }}
                                </a>
                                {{--
                                <button class="flex-shrink-0 text-gray-500 hover:text-red-600">
                                    <x-heroicon-o-x class="w-4 h-4" />
                                </button>
                                --}}
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
@overwrite