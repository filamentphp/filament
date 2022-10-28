<form wire:submit.prevent="authenticate" class="space-y-8">
    {{ $this->form }}

    <x-filament::button type="submit" form="authenticate" class="w-full"  wire:target="authenticate">
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>
</form>
