<form wire:submit.prevent="save">

    <x-filament-fields :fields="$fields" />

    <x-filament-button label="Update Role" />

</form>