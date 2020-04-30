<form wire:submit.prevent="save">

    <x-filament-tabs tab="info" :tabs="['info' => 'Info', 'roles' => 'Roles']">

        <x-filament-tab id="info">

            <x-filament-fields :fields="$fields->get('info')" />

        </x-filament-tab>

        <x-filament-tab id="roles">

            <x-filament-fields :fields="$fields->get('roles')" />

        </x-filament-tab>

        <button type="submit" class="btn">{{ __('Save') }}</button>

    </x-filament-tabs>

</form>