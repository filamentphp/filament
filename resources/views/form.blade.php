<form wire:submit.prevent="saveAndStay">
    @foreach ($fields as $field)
        @if ($field->view)
            @include($field->view)
        @else
            @include('filament::fields.' . $field->type)
        @endif
    @endforeach
    <x-filament-button label="Save">
        @if ($goback)
            <x-slot name="dropdown">
                <button wire:click="saveAndGoBack">{{ __('Save & Go Back') }}</button>
            </x-slot>
        @endif
    </x-filament-button>
</form>
