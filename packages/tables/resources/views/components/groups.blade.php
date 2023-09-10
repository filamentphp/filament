@props([
    'dropdownOnDesktop' => false,
    'groups',
    'triggerAction',
])

@php
    $labelClasses = 'text-sm font-medium leading-6 text-gray-950 dark:text-white';
@endphp

<div
    x-data="{
        direction: $wire.$entangle('tableGroupingDirection', true),
        group: $wire.$entangle('tableGrouping', true),
    }"
    x-init="
        $watch('group', function (newGroup, oldGroup) {
            if (newGroup && direction) {
                return
            }

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
        placement="bottom-start"
        shift
        width="xs"
        wire:key="{{ $this->getId() }}.table.grouping"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->class([
                    'sm:hidden' => ! $dropdownOnDesktop,
                ])
        "
    >
        <x-slot name="trigger">
            <span
                @class([
                    'inline-flex',
                    '-mx-2' => $triggerAction->isIconButton(),
                ])
            >
                {{ $triggerAction }}
            </span>
        </x-slot>

        <div class="grid gap-y-6 p-6">
            <label class="grid gap-y-2">
                <span class="{{ $labelClasses }}">
                    {{ __('filament-tables::table.grouping.fields.group.label') }}
                </span>

                <x-filament::input.wrapper>
                    <x-filament::input.select
                        x-model="group"
                        x-on:change="resetCollapsedGroups()"
                    >
                        <option value="">-</option>

                        @foreach ($groups as $group)
                            <option value="{{ $group->getId() }}">
                                {{ $group->getLabel() }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>

            <label x-cloak x-show="group" class="grid gap-y-2">
                <span class="{{ $labelClasses }}">
                    {{ __('filament-tables::table.grouping.fields.direction.label') }}
                </span>

                <x-filament::input.wrapper>
                    <x-filament::input.select x-model="direction">
                        <option value="asc">
                            {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                        </option>

                        <option value="desc">
                            {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                        </option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
        </div>
    </x-filament::dropdown>

    @if (! $dropdownOnDesktop)
        <div class="hidden items-center gap-x-3 sm:flex">
            <label>
                <span class="sr-only">
                    {{ __('filament-tables::table.grouping.fields.group.label') }}
                </span>

                <x-filament::input.wrapper>
                    <x-filament::input.select
                        x-model="group"
                        x-on:change="resetCollapsedGroups()"
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
                </x-filament::input.wrapper>
            </label>

            <label x-cloak x-show="group">
                <span class="sr-only">
                    {{ __('filament-tables::table.grouping.fields.direction.label') }}
                </span>

                <x-filament::input.wrapper>
                    <x-filament::input.select x-model="direction">
                        <option value="asc">
                            {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                        </option>

                        <option value="desc">
                            {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                        </option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
        </div>
    @endif
</div>
