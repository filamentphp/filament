<x-filament::form submit="update">
    <x-filament::input-group-stacked name="user.name" :label="__('Name')" required>
        <x-filament::input name="name" wire:model="user.name" id="user.name" required tabindex="1" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked name="user.email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" name="email" wire:model.lazy="user.email" id="user.email" required autocomplete="email" tabindex="2" />
    </x-filament::input-group-stacked>

    <x-filament::form-cols>
        <x-filament::input-group-stacked name="password" :label="__('New Password')">   
            <x-filament::input type="password" name="password" wire:model.lazy="password" id="password" autocomplete="new-password" tabindex="3" />
            <x-slot name="hint">
                {{ __('Optional') }}
            </x-slot>
            <x-slot name="help">
                {{ __('Leave blank to keep current password.') }}
            </x-slot>
        </x-filament::input-group-stacked>

        <x-filament::input-group-stacked name="password_confirmation" :label="__('Confirm New Password')">   
            <x-filament::input type="password" name="password_confirmation" wire:model.lazy="password_confirmation" id="password_confirmation" autocomplete="new-password" tabindex="4" />
        </x-filament::input-group-stacked>
    </x-filament::form-cols>

    <x-filament::button type="submit" class="btn-primary" wire:loading.attr="disabled" tabindex="5">
        {{ __('Update Account') }}
    </x-filament::button>
</x-filament::form>