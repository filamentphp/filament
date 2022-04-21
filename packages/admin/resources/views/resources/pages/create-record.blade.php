<x-filament::page class="filament-resources-create-record-page">
    <x-filament::form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::form.actions :actions="$this->getCachedFormActions()" />
    </x-filament::form>

    @if (config('filament.shortcuts.enabled'))
        <div x-data
             x-init="
                Mousetrap.bindGlobal(@js(config('filament.shortcuts.bindings.save')), $event => {
                    $event.preventDefault()
                    
                    $wire.create()
                })
            "
        ></div>
    @endif
</x-filament::page>
