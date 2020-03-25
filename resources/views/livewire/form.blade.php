<form wire:submit.prevent="save">
    @include('filament::partials.fields', ['fields' => $fields])
    <x-filament-button label="Save">
        @if ($goback)
            <x-slot name="dropdown">
                <button wire:click.prevent="saveAndGoBack">{{ __('Save & Go Back') }}</button>
            </x-slot>
        @endif
    </x-filament-button>
</form>
