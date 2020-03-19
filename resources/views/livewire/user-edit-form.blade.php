<form wire:submit.prevent="update">

    <x-filament-alert :type="session('alert.type')" :message="session('alert.message')" />

    <x-filament-input-group wire:key="input-name" id="name" label="Name" required>
        <x-filament-input id="name" name="name" wire:model="name" required />
    </x-filament-input-group>

    <x-filament-input-group wire:key="input-email" id="email" label="E-Mail Address" required>
        <x-filament-input id="email" type="email" name="email" wire:model="email" required />
    </x-filament-input-group>
    
    <x-filament-button wire:key="input-submit" label="Update" />

</form>