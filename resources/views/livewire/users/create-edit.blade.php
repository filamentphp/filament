<form wire:submit.prevent="save">

    <x-filament-tabs tab="account" :tabs="['account' => 'Account', 'permissions' => 'Permissions']">

        <x-filament-tab id="account">

            <x-filament-fields :fields="$fields" group="account" />

        </x-filament-tab>
    
        <x-filament-tab id="permissions">
            
            <x-filament-fields :fields="$fields" group="permissions" />

        </x-filament-tab>

        <button type="submit" class="btn">{{ __('Save') }}</button>

    </x-filament-tabs>

</form>