<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>

    @foreach (['inline' => false, 'teleported' => true] as $name => $teleport)
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button data-testid="{{ $name }}-outer-trigger">
                    {{ ucfirst($name) }} dropdown
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown :teleport="$teleport">
                <x-slot name="trigger">
                    <x-filament::button data-testid="{{ $name }}-inner-trigger">
                        Nested dropdown
                    </x-filament::button>
                </x-slot>

                <x-filament::button data-testid="{{ $name }}-inner-action">
                    Nested action
                </x-filament::button>
            </x-filament::dropdown>
        </x-filament::dropdown>
    @endforeach
</x-filament-panels::page>
