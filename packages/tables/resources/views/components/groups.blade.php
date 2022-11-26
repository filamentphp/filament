@props([
    'groups',
])

<x-filament::dropdown
    {{ $attributes->class(['sm:hidden']) }}
    placement="bottom-start"
    shift
    wire:key="{{ $this->id }}.table.grouping"
>
    <x-slot name="trigger">
        <x-filament::icon-button
            icon="heroicon-o-rectangle-stack"
            :label="__('filament-tables::table.buttons.group.label')"
        />
    </x-slot>

    <div class="px-4 pb-4 pt-3">
        <label class="space-y-1">
            <span class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300">
                {{ __('filament-tables::table.fields.grouping.label') }}
            </span>

            <x-filament::input.select wire:model="tableGrouping" class="w-full">
                <option value="">-</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->getId() }}">{{ $group->getLabel() }}</option>
                @endforeach
            </x-filament::input.select>
        </label>
    </div>
</x-filament::dropdown>

<label class="hidden sm:block">
    <span class="sr-only">
        {{ __('filament-tables::table.fields.grouping.label') }}
    </span>

    <x-filament::input.select wire:model="tableGrouping" size="sm" class="text-sm">
        <option value="">{{ __('filament-tables::table.fields.grouping.placeholder') }}</option>
        @foreach ($groups as $group)
            <option value="{{ $group->getId() }}">{{ $group->getLabel() }}</option>
        @endforeach
    </x-filament::input.select>
</label>
