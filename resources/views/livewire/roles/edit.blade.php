<form wire:submit.prevent="save">

    <h2>{{ __('filament::permissions.role', ['role' => $role->name]) }}</h2>

    <x-filament-button label="Save" />

</form>