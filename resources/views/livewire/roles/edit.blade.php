<form wire:submit.prevent="save">

    <h2 class="text-xl lg:text-2xl font-semibold mb-4">{{ $title }}</h2>

    <x-filament-fields :fields="$fields" />

    <x-filament-button label="filament::permissions.role.update" />

</form>