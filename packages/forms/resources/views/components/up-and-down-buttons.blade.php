@props([
    'component' => '', // repeater|builder
    'isItemMovementDisabled',
    'loop',
    'statePath',
    'uuid'
])

@if (!$isItemMovementDisabled && config('forms.components.'.$component.'.up_and_down_buttons'))
    @unless($loop->first)
        <li>
            <button
                title="{{ __('forms::components.'.$component.'.buttons.move_item_up.label') }}"
                type="button"
                wire:click.stop="dispatchFormEvent('{{ $component }}::moveItemUp', '{{ $statePath }}', '{{ $uuid }}')"
                @class([
                    'flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500',
                    'dark:border-gray-700' => config('forms.dark_mode'),
                ])
            >
                <span class="sr-only">
                    {{ __('forms::components.repeater.buttons.move_item_up.label') }}
                </span>

                <x-heroicon-s-chevron-up class="w-4 h-4" />
            </button>
        </li>
    @endunless
    @unless($loop->last)
        <li>
            <button
                title="{{ __('forms::components.'.$component.'.buttons.move_item_down.label') }}"
                type="button"
                wire:click.stop="dispatchFormEvent('{{ $component }}::moveItemDown', '{{ $statePath }}', '{{ $uuid }}')"
                @class([
                    'flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500',
                    'dark:border-gray-700' => config('forms.dark_mode'),
                ])
            >
                <span class="sr-only">
                    {{ __('forms::components.repeater.buttons.move_item_down.label') }}
                </span>

                <x-heroicon-s-chevron-down class="w-4 h-4" />
            </button>
        </li>
    @endunless
@endunless
