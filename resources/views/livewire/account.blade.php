<x-filament::form>
    <x-filament::input-group-stacked field="user.name" for="name" :label="__('Name')" required>
        <x-filament::input field="user.name" wire:model="user.name" id="name" required tabindex="1" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked field="user.email" for="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" field="user.email" wire:model.lazy="user.email" id="email" required autocomplete="email" tabindex="2" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked field="avatar" for="photo" :label="__('Photo')">
        <div>
            <input type="file" wire:model="avatar" id="photo" tabindex="3" />
        </div>
        <x-slot name="hint">
            {{ __('Optional') }}
        </x-slot>
        <x-slot name="help">
            {{ __('Your user avatar photo.') }}
        </x-slot>
    </x-filament::input-group-stacked>

    <x-filament::fieldset :legend="__('Update Password')" cols="2">
        <x-filament::input-group-stacked field="password" for="password" :label="__('New Password')">   
            <x-filament::input type="password" field="password" wire:model.lazy="password" id="password" autocomplete="new-password" tabindex="4" />
            <x-slot name="hint">
                {{ __('Optional') }}
            </x-slot>
            <x-slot name="help">
                {{ __('Leave blank to keep current password.') }}
            </x-slot>
        </x-filament::input-group-stacked>

        <x-filament::input-group-stacked field="password_confirmation" for="password_confirmation" :label="__('Confirm New Password')">   
            <x-filament::input type="password" field="password_confirmation" wire:model.lazy="password_confirmation" id="password_confirmation" autocomplete="new-password" tabindex="5" />
        </x-filament::input-group-stacked>
    </x-filament::fieldset>

    <x-filament::button type="submit" class="btn-primary" wire:loading.attr="disabled" tabindex="6">
        {{ __('Update Account') }}
    </x-filament::button>
</x-filament::form>