<form wire:submit.prevent="update">

    <x-filament-input-group wire:key="input-name" id="name" label="Name" required>
        <x-filament-input id="name" name="name" wire:model="name" required />
    </x-filament-input-group>

    <x-filament-input-group wire:key="input-email" id="email" label="E-Mail Address" required>
        <x-filament-input id="email" type="email" name="email" wire:model="email" required />
    </x-filament-input-group>

    <x-filament-input-group>
        <x-filament-checkbox name="is_super_admin" label="Super admin?" wire:model="is_super_admin" />
        <x-slot name="info">
            Super admins have access to the entire admin.
        </x-slot>
    </x-filament-input-group>

    @if (count($roles))
        <fieldset>
            <legend>Roles</legend>
            <ol>
                @foreach($roles as $role)
                    <li>{{ $role->name }}</li>
                @endforeach
            </ol>
        </fieldset>
    @endif
    
    <x-filament-button wire:key="input-submit" label="Update" />

</form>