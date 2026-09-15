<x-filament-panels::page>
    <button type="button" wire:click="prependItem" data-testid="prepend">
        Prepend item
    </button>

    <ul>
        @foreach ($items as $item)
            <li wire:key="row-{{ $item }}">
                <span>{{ $item }}</span>

                {{--
                    The `x-text` binding is what lets a morph initialise the dropdown twice:
                    its effect runs `mutateDom` while the morph clones the incoming row,
                    which flushes Alpine's mutation observer mid-morph, so the removal and
                    re-insertion of a keyed row land in different batches.
                --}}
                <div x-data="{ expanded: false }">
                    <span
                        x-text="expanded ? 'Full summary of {{ $item }}' : 'Summary of {{ $item }}'"
                    >
                        Summary of {{ $item }}
                    </span>
                    <button
                        type="button"
                        x-on:click="expanded = ! expanded"
                        x-text="expanded ? 'Show less' : 'Show more'"
                    >
                        Show more
                    </button>
                </div>

                {{--
                    Deliberately without a `wire:key`: the morph then keys the panel on its
                    client-generated `id`, which the server HTML does not have, so the panel
                    is replaced while the trigger survives.
                --}}
                <x-filament::dropdown placement="bottom-end">
                    <x-slot name="trigger">
                        <button type="button">Open</button>
                    </x-slot>

                    <x-filament::dropdown.list>
                        <x-filament::dropdown.list.item>
                            One
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item>
                            Two
                        </x-filament::dropdown.list.item>
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            </li>
        @endforeach
    </ul>
</x-filament-panels::page>
