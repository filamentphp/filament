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
            <label for="{{ $id }}" class="cursor-pointer rounded-full shadow flex overflow-hidden">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" alt="{{ $user->name }}" class="h-full object-cover" width="{{ $size }}" height="{{ $size }}" loading="lazy">
                @else 
                    <x-filament-avatar :size="$size" :user="$user" />
                @endif
            </label>
            @if ($user->avatar && $deleteMethod)
                <button type="button"     
                    wire:click="{{ $deleteMethod }}"   
                    class="absolute top-0 right-0 w-4 h-4 rounded-full bg-gray-800 text-white flex items-center justify-center hover:bg-red-700 transition-colors duration-200">
                    <span class="sr-only">{{ __('Delete Avatar') }}</span>
                    <x-heroicon-o-x class="w-3 h-3" />
                </button>
            @endif
        </div>
        <div class="flex-grow relative">
            <label class="btn" for="{{ $id }}">{{ __($buttonLabel) }}</label>
            <input type="file" 
                {{ $modelDirective }}="{{ $model }}"
                value="{{ $value }}"
                class="sr-only" 
                id="{{ $id }}"
                @foreach ($extraAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach  
            />
            <div x-show="isUploading" :aria-hidden="!isUploading" class="absolute bottom-0 -mb-3 w-48 max-w-full">
                <x-filament::progress progress="progress" />     
            </div>
        </div>
    </div>
@overwrite