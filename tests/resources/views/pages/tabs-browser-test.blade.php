<x-filament-panels::page>
    <form
        wire:submit="save"
        data-testid="tabs-form"
        x-data="{ showContact: true, showAll: true, showOverflowChoice: false }"
    >
        @if ($this->showKeyboardTabs)
            <button
                type="button"
                data-testid="hide-contact"
                x-on:click="showContact = false"
            >
                Hide contact
            </button>
            <button
                type="button"
                data-testid="hide-all"
                x-on:click="showAll = false"
            >
                Hide tabs
            </button>
            <button
                type="button"
                data-testid="remove-tab"
                wire:click="$set('showSecondTab', false)"
            >
                Remove details
            </button>
            <button
                type="button"
                data-testid="remove-zero"
                wire:click="$set('showZeroTab', false)"
            >
                Remove zero
            </button>
        @endif

        @if ($this->showEnclosingDropdown)
            <x-filament::dropdown
                :menu="false"
                data-testid="enclosing-dropdown"
            >
                <x-slot name="trigger">
                    <x-filament::button
                        data-testid="enclosing-dropdown-trigger"
                    >
                        Open settings
                    </x-filament::button>
                </x-slot>

                {{ $this->form }}
            </x-filament::dropdown>
        @else
            {{ $this->form }}
        @endif

        <x-filament::button type="submit" data-testid="save">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>
