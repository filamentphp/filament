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
</x-filament-panels::page>
