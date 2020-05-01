<form wire:submit.prevent="save">
    <x-filament-tabs :tabs="$groupedFields->keys()->all()">
        @foreach($groupedFields as $key => $fields)
            <x-filament-tab :id="$key">
                <x-filament-fields :fields="$fields" />
            </x-filament-tab>
        @endforeach
        <button type="submit" class="btn">{{ __('Save') }}</button>
    </x-filament-tabs>
</form>