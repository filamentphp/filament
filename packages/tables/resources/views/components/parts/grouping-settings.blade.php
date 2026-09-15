@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\View\TablesRenderHook;

    $groups = $table->getGroups();
    $areGroupingSettingsInDropdownOnDesktop = $table->areGroupingSettingsInDropdownOnDesktop();
    $isGroupingDirectionSettingHidden = $table->isGroupingDirectionSettingHidden();
@endphp

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE, scopes: static::class) }}

@if ($table->areGroupingSettingsVisible())
    <div
        x-data="{
            grouping: $wire.$entangle('tableGrouping', true),
            group: null,
            direction: null,
        }"
        x-init="
            if (grouping) {
                ;[group, direction] = grouping.split(':')
                direction ??= 'asc'
            }

            $watch('grouping', function () {
                if (! grouping) {
                    group = null
                    direction = null

                    return
                }

                ;[group, direction] = grouping.split(':')
                direction ??= 'asc'
            })

            $watch('direction', function () {
                grouping = group ? `${group}:${direction}` : null
            })

            $watch('group', function (newGroup, oldGroup) {
                if (! newGroup) {
                    direction = null
                    grouping = group ? `${group}:${direction}` : null

                    return
                }

                if (oldGroup) {
                    grouping = group ? `${group}:${direction}` : null

                    return
                }

                direction ??= 'asc'
                grouping = group ? `${group}:${direction}` : null
            })
        "
        class="fi-ta-grouping-settings"
    >
        <x-filament::dropdown
            placement="bottom-start"
            shift
            width="xs"
            :wire:key="$this->getId() . '.table.grouping'"
            @class([
                'sm:fi-hidden' => ! $areGroupingSettingsInDropdownOnDesktop,
            ])
        >
            <x-slot name="trigger">
                {{ $table->getGroupRecordsTriggerAction() }}
            </x-slot>

            <div class="fi-ta-grouping-settings-fields">
                <label>
                    <span>
                        {{ __('filament-tables::table.grouping.fields.group.label') }}
                    </span>

                    <x-filament::input.wrapper>
                        <x-filament::input.select
                            x-model="group"
                            x-on:change="resetCollapsedGroups()"
                        >
                            <option value="">-</option>

                            @foreach ($groups as $groupOption)
                                <option
                                    value="{{ $groupOption->getId() }}"
                                >
                                    {{ $groupOption->getLabel() }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>

                @if (! $isGroupingDirectionSettingHidden)
                    <label x-cloak x-show="group">
                        <span>
                            {{ __('filament-tables::table.grouping.fields.direction.label') }}
                        </span>

                        <x-filament::input.wrapper>
                            <x-filament::input.select
                                x-model="direction"
                            >
                                <option value="asc">
                                    {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                                </option>

                                <option value="desc">
                                    {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                                </option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </label>
                @endif
            </div>
        </x-filament::dropdown>

        @if (! $areGroupingSettingsInDropdownOnDesktop)
            <div class="fi-ta-grouping-settings-fields">
                <label>
                    <x-filament::input.wrapper
                        :prefix="__('filament-tables::table.grouping.fields.group.label')"
                    >
                        <x-filament::input.select
                            x-model="group"
                            x-on:change="resetCollapsedGroups()"
                        >
                            <option value="">-</option>

                            @foreach ($groups as $groupOption)
                                <option
                                    value="{{ $groupOption->getId() }}"
                                >
                                    {{ $groupOption->getLabel() }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>

                @if (! $isGroupingDirectionSettingHidden)
                    <label x-cloak x-show="group">
                        <span class="fi-sr-only">
                            {{ __('filament-tables::table.grouping.fields.direction.label') }}
                        </span>

                        <x-filament::input.wrapper>
                            <x-filament::input.select
                                x-model="direction"
                            >
                                <option value="asc">
                                    {{ __('filament-tables::table.grouping.fields.direction.options.asc') }}
                                </option>

                                <option value="desc">
                                    {{ __('filament-tables::table.grouping.fields.direction.options.desc') }}
                                </option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </label>
                @endif
            </div>
        @endif
    </div>
@endif

{{ FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER, scopes: static::class) }}
