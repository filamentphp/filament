@extends('filament::layouts.field-group')

@section('field')
    <div x-cloak
         x-data="{ isUploading: false, progress: 0 }"
         x-on:livewire-upload-start="isUploading = true"
         x-on:livewire-upload-finish="isUploading = false"
         x-on:livewire-upload-error="isUploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress"
         class="flex items-center space-x-4">
        <div class="flex-shrink-0 relative">
            <div class="rounded-full shadow-sm flex overflow-hidden">
                @if ($field->avatar && ! $errors->has($field->name))
                    <img src="{{ $field->avatar->temporaryUrl() }}" alt="{{ $field->user->name }}"
                         class="h-full object-cover" width="{{ $field->size }}" height="{{ $field->size }}"
                         loading="lazy">
                @else
                    <x-filament::modal class="flex">
                        <x-filament-avatar :size="$field->size" :user="$field->user" />

                        <x-slot name="content">
                            <x-filament-avatar :size="320" :user="$field->user" />
                        </x-slot>
                    </x-filament::modal>
                @endif
            </div>

            @if ($field->user->avatar)
                <button type="button"
                        wire:click="deleteFile($field->name)"
                        class="absolute top-0 right-0 w-4 h-4 rounded-full bg-gray-800 text-white flex items-center justify-center hover:bg-red-700 transition-colors duration-200">
                    <span class="sr-only">{{ __('filament::avatar.delete') }}</span>
                    <x-heroicon-o-x class="w-3 h-3" />
                </button>
            @endif
        </div>

        <div class="flex-grow relative">
            <label class="btn btn-sm" for="{{ $field->id }}">{{ __($field->buttonLabel) }}</label>

            <input type="file"
                {{ $field->nameAttribute }}="{{ $field->name }}"
                class="sr-only"
                id="{{ $field->id }}"
                {{ Filament\format_attributes($field->attributes) }}
            />

            <div x-show="isUploading" :aria-hidden="!isUploading" class="absolute bottom-0 -mb-3 w-48 max-w-full">
                <x-filament::progress progress="progress" />
            </div>
        </div>
    </div>
@overwrite
