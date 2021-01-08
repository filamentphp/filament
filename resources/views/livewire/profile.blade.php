<x-filament::form class="space-y-6">
    <x-filament::fields :fields="$this->fields()" />

    <x-filament::button 
        type="submit" 
        class="btn-primary" 
        wire:loading.attr="disabled"
    >
        {{ __('Update Profile') }}
    </x-filament::button>
</x-filament::form>