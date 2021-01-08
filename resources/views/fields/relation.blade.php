@extends('filament::layouts.field-group')

@section('field')
    <div class="space-y-4">
        <x-filament::select
            :id="$field->id"
            :error-key="$field->error ?? $field->model"
            :extra-attributes="$field->extraAttributes"
            wire:change="{{ $field->addMethod }}($event.target.value)"
        >
            @if ($field->placeholder)
                <option value="">{{ __($field->placeholder) }}</option>
            @endif
            @foreach ($field->options as $value => $label)
                <option 
                    value="{{ $value }}" 
                    @if (in_array($value, $this->{$field->model}))
                        disabled
                    @endif
                >{{ $label }}</option>
            @endforeach
        </x-filament::select>

        @if ($this->{$field->model})
            <ol 
                class="rounded shadow-sm border border-gray-300 divide-y divide-gray-300"
                @if ($field->sortMethod)
                    wire:sortable="{{ $field->sortMethod }}"
                @endif    
            >
                @foreach ($this->{$field->model} as $key => $id)
                    <li 
                        class="w-full px-3 py-2 flex items-center justify-between space-x-6"
                        @if ($field->sortMethod)
                            wire:key="value-{{ $key }}"
                            wire:sortable.item="{{ $id }}" 
                        @endif    
                    >
                        <div class="flex-grow flex items-center space-x-3">
                            @if ($field->sortMethod)
                                <button 
                                    class="flex-shrink-0 text-gray-300 hover:text-gray-600 transition-colors duration-200 flex cursor-move"
                                    wire:sortable.handle
                                >
                                    <x-heroicon-o-menu-alt-4 class="w-4 h-4" aria-hidden="true" />
                                    <span class="sr-only">{{ __('Sort item') }}</span>
                                </button>
                            @endif
                            <div class="flex-grow overflow-x-auto">
                                <span class="text-sm leading-tight">{{ isset($field->options[$id]) ? $field->options[$id] : $id }}</span>
                            </div>
                        </div>
                        @if ($field->deleteMethod)
                            <button 
                                type="button" 
                                class="flex-shrink-0 text-gray-500 hover:text-red-600 transition-colors duration-200 flex"
                                wire:click="{{ $field->deleteMethod }}('{{ $key }}')"
                            >
                                <x-heroicon-o-x class="w-4 h-4" />
                                <span class="sr-only">{{ __('Remove item') }}</span>
                            </button>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
@overwrite