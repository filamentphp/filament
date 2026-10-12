<x-filament-panels::page>
    <form wire:submit="save(true)">
        {{ $this->form }}

        <x-filament::button type="submit" data-testid="save">
            Save
        </x-filament::button>
    </form>

    @foreach (array_keys($this->data) as $fieldName)
        <p>
            Saved {{ $fieldName }}:
            <output data-testid="saved-{{ $fieldName }}">
                {{ json_encode($saved[$fieldName] ?? null) }}
            </output>
        </p>
        <p>
            Updates to {{ $fieldName }}:
            <output data-testid="updated-{{ $fieldName }}">
                {{ json_encode($updatedStates[$fieldName] ?? []) }}
            </output>
        </p>
    @endforeach
</x-filament-panels::page>
