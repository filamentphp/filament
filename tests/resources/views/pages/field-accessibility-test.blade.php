<x-filament-panels::page>
    <form wire:submit="save" novalidate data-testid="membership-form">
        <p id="privacy-note">
            Your details are only shared with the membership team.
        </p>

        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save">
            Save
        </x-filament::button>
        @if ($this->saved)
            <span data-testid="saved">Saved</span>
        @endif
    </form>

    <form novalidate data-testid="additional-form">
        <p id="custom-file-note">
            Only share documents intended for the membership team.
        </p>

        {{ $this->additionalForm }}

        <x-filament::button
            type="button"
            wire:click="validateAdditionalFields"
            data-testid="validate-additional"
        >
            Validate additional fields
        </x-filament::button>
        <x-filament::button
            type="button"
            wire:click="acceptAdditionalFields"
            data-testid="accept-additional"
        >
            Accept additional fields
        </x-filament::button>
    </form>
</x-filament-panels::page>
