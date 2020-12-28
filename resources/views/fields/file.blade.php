@extends('filament::layouts.field-group')

@pushonce('head')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.css">
@endpushonce

@pushonce('js')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
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
                <ol 
                    class="relative flex flex-wrap -m-2" 
                    @if ($field->sortMethod)
                        wire:sortable="{{ $field->sortMethod }}"
                    @endif
                >
                    @foreach ($field->value as $file)
                        <li 
                            wire:key="file-{{ $file }}"
                            @if ($field->sortMethod)
                                wire:sortable.item="{{ $file }}" 
                            @endif
                            class="w-full md:w-1/2 max-w-full p-2"
                        >
                            <div class="p-2 bg-white shadow-sm rounded border border-gray-300 flex items-center space-x-4">
                                @if (exif_imagetype(Filament::storage()->path($file)))
                                    <a href="{{ Filament::storage()->url($file) }}" target="_blank" rel="noopener noreferrer" class="flex-shrink-0">
                                        <x-filament-image :src="$file" alt="{{ $file }}" :manipulations="[ 'w' => 48, 'h' => 48, 'fit' => 'crop' ]" width="48px" height="48px" loading="lazy" class="w-12 h-12 rounded" />    
                                    </a>
                                @endif
                                <div class="flex-grow overflow-scroll flex flex-col leading-tight">
                                    <a href="{{ Filament::storage()->url($file) }}" target="_blank" rel="noopener noreferrer" class="text-sm link">
                                        {{ $file }}
                                    </a>
                                    <dl class="font-mono text-xs text-gray-500">
                                        <dt class="sr-only">{{ __('MIME Type') }}</dt>
                                        <dd>{{ Filament::storage()->getMimeType($file) }}</dd>
                                        <dt class="sr-only">{{ __('File Size') }}</dt>
                                        <dd>{{ Filament::formatBytes(Filament::storage()->size($file)) }}</dd>
                                    </dl>
                                </div>
                                @if ($field->deleteMethod)
                                    <button 
                                        type="button" 
                                        wire:click="{{ $field->deleteMethod }}('{{ $file }}')" 
                                        class="flex-shrink-0 text-gray-500 hover:text-red-600 transition-colors duration-200"
                                    >
                                        <x-heroicon-o-x class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
@overwrite