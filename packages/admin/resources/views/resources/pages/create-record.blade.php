<x-filament::page class="filament-resources-create-record-page">
    <x-filament::form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::form.actions :actions="$this->getCachedFormActions()" />
    </x-filament::form>

    @if (config('filament.shortcuts_enabled'))
        <div x-data
             x-init="
                Mousetrap.bindGlobal(['ctrl+s', 'command+s'], $event => {
                    $event.preventDefault()
                    
                    $wire.create()
                })
            "
        ></div>
    @endif
</x-filament::page>
