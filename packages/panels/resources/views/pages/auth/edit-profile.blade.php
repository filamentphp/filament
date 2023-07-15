<x-filament::layouts.card>
    <x-slot name="subheading">
        {{ $this->backAction }}
    </x-slot>

    <form wire:submit="save" class="grid gap-y-8">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </form>
</x-filament::layouts.card>
