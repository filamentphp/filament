<x-filament::page.simple>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/register.actions.login.before') }}

            {{ $this->loginAction }}
        </x-slot>
    @endif

    <x-filament::form wire:submit="register">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>
</x-filament::page.simple>
