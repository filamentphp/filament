<form wire:submit.prevent="authenticate" class="space-y-8">
    {{ $this->form }}

    <x-filament::button
        type="submit"
        form="authenticate"
        class="w-full"
        dusk="filament.admin.login.button"
    >
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>
</form>
