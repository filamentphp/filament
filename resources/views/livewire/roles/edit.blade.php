<form wire:submit.prevent="save">

    <h2 class="text-xl lg:text-2xl font-semibold mb-4">{{ $title }}</h2>

    <x-filament-fields :fields="$fields" />

    <button type="submit" class="btn">{{ __('Save') }}</button>

</form>