<form wire:submit.prevent="save">

    <h2 class="text-xl lg:text-2xl font-semibold mb-4">{{ $title }}</h2>

    <x-filament-tabs tab="info" :tabs="['info' => 'Info', 'permissions' => 'Permissions']">

        <x-filament-tab id="info">

            <x-filament-fields :fields="$fields" group="info" />

        </x-filament-tab>

        <x-filament-tab id="permissions">

            <x-filament-fields :fields="$fields" group="permissions" />

        </x-filament-tab>

        <button type="submit" class="btn">{{ __('Save') }}</button>

    </x-filament-tabs>

</form>