<x-filament::form>
    <x-filament::input-group field="user.name" for="name" :label="__('Name')" required>
        <x-filament::input field="user.name" wire:model="user.name" id="name" required tabindex="1" />
    </x-filament::input-group>

    <x-filament::input-group field="user.email" for="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" field="user.email" wire:model.lazy="user.email" id="email" required autocomplete="email" tabindex="2" />
    </x-filament::input-group>

    <x-filament::input-group field="avatar" for="photo" :label="__('Avatar')">
        <x-filament::input-avatar field="avatar" wire:model="avatar" :avatar="$avatar" :user="$user" delete="deleteAvatar" id="photo" tabindex="3" />
        <x-slot name="hint">
            {{ __('Optional') }}
        </x-slot>
    </x-filament::input-group>

    <x-filament::fieldset :legend="__('Update Password')" class="grid grid-cols-1 gap-2 lg:gap-6 lg:grid-cols-2">
        <x-filament::input-group field="password" for="password" :label="__('New Password')">   
            <x-filament::input type="password" field="password" wire:model="password" id="password" autocomplete="new-password" tabindex="4" />
            <x-slot name="hint">
                {{ __('Optional') }}
            </x-slot>
            <x-slot name="help">
                {{ __('Leave blank to keep current password.') }}
            </x-slot>
        </x-filament::input-group>

        <x-filament::input-group field="password_confirmation" for="password_confirmation" :label="__('Confirm New Password')">   
            <x-filament::input type="password" field="password_confirmation" wire:model="password_confirmation" id="password_confirmation" autocomplete="new-password" tabindex="5" />
        </x-filament::input-group>
    </x-filament::fieldset>

    <x-filament::button type="submit" class="btn-primary" wire:loading.attr="disabled" tabindex="6">
        {{ __('Update Account') }}
    </x-filament::button>
</x-filament::form>