<x-filament-panels::page>
    <form wire:submit="save" x-data="{ isEditorVisible: false }">
        <x-filament::button
            type="button"
            data-testid="reveal-editor"
            x-on:click="isEditorVisible = true"
        >
            Show article editor
        </x-filament::button>
        {{ $this->form }}
        <x-filament::button type="submit" data-testid="save-editor">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>
