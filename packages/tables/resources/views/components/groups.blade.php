@props([
    'groups',
])

<div
    x-data="{
        direction: $wire.entangle('tableGroupingDirection'),
        group: $wire.entangle('tableGrouping'),
    }"
    x-init="
        $watch('group', function (newGroup, oldGroup) {
            if (! newGroup) {
                direction = null

                return
            }

            if (oldGroup) {
                return
            }

            direction = 'asc'
        })
    "
>
    <x-filament::dropdown
        {{ $attributes->class(['sm:hidden']) }}
        placement="bottom-start"
        shift
        wire:key="{{ $this->id }}.table.grouping"
    >
        <x-slot name="trigger">
            <x-filament::icon-button
                icon="heroicon-m-rectangle-stack"
                icon-alias="tables::grouping.trigger"
                color="gray"
                :label="__('filament-tables::table.buttons.group.label')"
            />
        </x-slot>

        <div class="flex flex-col gap-4 px-4 pb-4 pt-3">
            <label class="space-y-1">
                <span
                    class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300"
                >
                    {{ __('filament-tables::table.grouping.fields.group.label') }}
                </span>

                <x-filament::input.select
                    x-model="group"
                    x-on:change="resetCollapsedGroups()"
                    class="w-full"
                >
                    <option value="">-</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->getId() }}">
                            {{ $group->getLabel() }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </label>

            <label x-show="group" x-cloak class="space-y-1">
                <span
                    class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300"
                >
                    {{ __('filament-tables::table.grouping.fields.direction.label') }}
                </span>

                <x-filament::input.select x-model="direction" class="w-full">
                    <option value="asc">
                        {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                    </option>
                    <option value="desc">
                        {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                    </option>
                </x-filament::input.select>
            </label>
        </div>
    </x-filament::dropdown>

    <div class="hidden items-center gap-1 sm:flex">
        <label>
            <span class="sr-only">
                {{ __('filament-tables::table.grouping.fields.group.label') }}
            </span>

            <x-filament::input.select
                x-model="group"
                x-on:change="resetCollapsedGroups()"
                size="sm"
                class="text-sm"
            >
                <option value="">
                    {{ __('filament-tables::table.grouping.fields.group.placeholder') }}
                </option>
                @foreach ($groups as $group)
                    <option value="{{ $group->getId() }}">
                        {{ $group->getLabel() }}
                    </option>
                @endforeach
            </x-filament::input.select>
        </label>

        <label x-show="group" x-cloak>
            <span class="sr-only">
                {{ __('filament-tables::table.grouping.fields.direction.label') }}
            </span>

            <x-filament::input.select
                x-model="direction"
                size="sm"
                class="text-sm"
            >
                <option value="asc">
                    {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                </option>
                <option value="desc">
                    {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                </option>
            </x-filament::input.select>
        </label>
    </div>
</div>
