@php
    use Filament\Support\Enums\GridDirection;
    use Filament\Tables\Enums\ColumnManagerResetActionPosition;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'applyAction',
    'columns' => null,
    'hasReorderableColumns',
    'hasToggleableColumns',
    'headingTag' => 'h3',
    'reorderAnimationDuration' => 300,
    'resetActionPosition' => ColumnManagerResetActionPosition::Header,
])

<div class="fi-ta-col-manager">
    <div
        x-data="filamentTableColumnManager({
                    columns: $wire.entangle('tableColumns'),
                    isLive: {{ $applyAction->isVisible() ? 'false' : 'true' }},
                })"
        class="fi-ta-col-manager-ctn"
    >
        <div class="fi-ta-col-manager-header">
            <{{ $headingTag }} class="fi-ta-col-manager-heading">
                {{ __('filament-tables::table.column_manager.heading') }}
            </{{ $headingTag }}>

            @if ($resetActionPosition === ColumnManagerResetActionPosition::Header)
                <div>
                    <x-filament::link
                        :attributes="
                            \Filament\Support\prepare_inherited_attributes(
                                new ComponentAttributeBag([
                                    'color' => 'danger',
                                    'tag' => 'button',
                                    'wire:click' => 'resetTableColumnManager',
                                    'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                    'wire:target' => 'resetTableColumnManager',
                                    'x-on:click' => 'resetDeferredColumns',
                                ])
                            )
                        "
                    >
                        {{ __('filament-tables::table.column_manager.actions.reset.label') }}
                    </x-filament::link>
                </div>
            @endif
        </div>

        <div
            @if ($hasReorderableColumns)
                x-sortable
                x-on:end.stop="reorderColumns($event.target.sortable.toArray())"
                data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
            @endif
            {{
                (new ComponentAttributeBag)
                    ->grid($columns, GridDirection::Column)
                    ->class(['fi-ta-col-manager-items'])
            }}
        >
            <template
                x-for="(column, index) in columns.filter((column) => ! column.isHidden && column.label)"
                x-bind:key="(column.type === 'group' ? 'group::' : 'column::') + column.name + '_' + index"
            >
                <div
                    @if ($hasReorderableColumns)
                        x-bind:x-sortable-item="column.type === 'group' ? 'group::' + column.name : 'column::' + column.name"
                    @endif
                >
                    <template x-if="column.type === 'group'">
                        <div class="fi-ta-col-manager-group">
                            <div class="fi-ta-col-manager-item">
                                <label class="fi-ta-col-manager-label">
                                    @if ($hasToggleableColumns)
                                        <input
                                            type="checkbox"
                                            class="fi-checkbox-input fi-valid"
                                            x-bind:id="'group-' + column.name"
                                            x-bind:checked="(groupedColumns[column.name] || {}).checked || false"
                                            x-bind:disabled="(groupedColumns[column.name] || {}).disabled || false"
                                            x-effect="$el.indeterminate = (groupedColumns[column.name] || {}).indeterminate || false"
                                            x-on:change="toggleGroup(column.name)"
                                        />
                                    @endif

                                    <span x-html="column.label"></span>
                                </label>

                                @if ($hasReorderableColumns)
                                    <button
                                        x-sortable-handle
                                        x-on:click.stop
                                        class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                        type="button"
                                    >
                                        {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                    </button>
                                @endif
                            </div>
                            <div
                                @if ($hasReorderableColumns)
                                    x-sortable
                                    x-on:end.stop="reorderGroupColumns($event.target.sortable.toArray(), column.name)"
                                    data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
                                @endif
                                class="fi-ta-col-manager-group-items"
                            >
                                <template
                                    x-for="
                                        (groupColumn, index) in
                                            column.columns.filter((column) => ! column.isHidden && column.label)
                                    "
                                    x-bind:key="'column::' + groupColumn.name + '_' + index"
                                >
                                    <div
                                        @if ($hasReorderableColumns)
                                            x-bind:x-sortable-item="'column::' + groupColumn.name"
                                        @endif
                                    >
                                        <div class="fi-ta-col-manager-item">
                                            <label
                                                class="fi-ta-col-manager-label"
                                            >
                                                @if ($hasToggleableColumns)
                                                    <input
                                                        type="checkbox"
                                                        class="fi-checkbox-input fi-valid"
                                                        x-bind:id="'column-' + groupColumn.name.replace('.', '-')"
                                                        x-bind:checked="(getColumn(groupColumn.name, column.name) || {}).isToggled || false"
                                                        x-bind:disabled="(getColumn(groupColumn.name, column.name) || {}).isToggleable === false"
                                                        x-on:change="toggleColumn(groupColumn.name, column.name)"
                                                    />
                                                @endif

                                                <span
                                                    x-html="groupColumn.label"
                                                ></span>
                                            </label>

                                            @if ($hasReorderableColumns)
                                                <button
                                                    x-sortable-handle
                                                    x-on:click.stop
                                                    class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                                    type="button"
                                                >
                                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="column.type !== 'group'">
                        <div class="fi-ta-col-manager-item">
                            <label class="fi-ta-col-manager-label">
                                @if ($hasToggleableColumns)
                                    <input
                                        type="checkbox"
                                        class="fi-checkbox-input fi-valid"
                                        x-bind:id="'column-' + column.name.replace('.', '-')"
                                        x-bind:checked="(getColumn(column.name, null) || {}).isToggled || false"
                                        x-bind:disabled="(getColumn(column.name, null) || {}).isToggleable === false"
                                        x-on:change="toggleColumn(column.name)"
                                    />
                                @endif

                                <span x-html="column.label"></span>
                            </label>

                            @if ($hasReorderableColumns)
                                <button
                                    x-sortable-handle
                                    x-on:click.stop
                                    class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                    type="button"
                                >
                                    {{ \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::Bars2, alias: \Filament\Tables\View\TablesIconAlias::REORDER_HANDLE) }}
                                </button>
                            @endif
                        </div>
                    </template>
                </div>
            </template>
        </div>

        @if ($applyAction->isVisible() || $resetActionPosition === ColumnManagerResetActionPosition::Footer)
            <div class="fi-ta-col-manager-actions-ctn">
                @if ($applyAction->isVisible())
                    {{ $applyAction }}
                @endif

                @if ($resetActionPosition === ColumnManagerResetActionPosition::Footer)
                    <x-filament::button
                        color="danger"
                        wire:click="resetTableColumnManager"
                        x-on:click="resetDeferredColumns"
                    >
                        {{ __('filament-tables::table.column_manager.actions.reset.label') }}
                    </x-filament::button>
                @endif
            </div>
        @endif
    </div>
</div>
