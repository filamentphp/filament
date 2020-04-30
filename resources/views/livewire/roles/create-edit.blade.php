<form wire:submit.prevent="save">

    <x-filament-tabs tab="info" :tabs="['info' => 'Info', 'permissions' => 'Permissions']">

        <x-filament-tab id="info">

            <x-filament-fields :fields="$fields->get('info')" />

        </x-filament-tab>

        <x-filament-tab id="permissions">

            <x-filament-fields :fields="$fields->get('permissions')" />

        </x-filament-tab>

        <button type="submit" class="btn">{{ __('Save') }}</button>

    </x-filament-tabs>

</form>