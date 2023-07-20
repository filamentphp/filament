<x-filament::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    <x-filament::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>
</x-filament::page.simple>
