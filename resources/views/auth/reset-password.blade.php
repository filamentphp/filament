<x-forms::container :form="$this->getForm()" class="space-y-6">
    <x-filament::button type="submit" color="primary" class="w-full">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />

        {{ __('filament::auth/reset-password.buttons.submit.label') }}
    </x-filament::button>
</x-forms::container>
