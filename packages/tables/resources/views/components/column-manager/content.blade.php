@php
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
@endphp

@props([
    'columns' => null,
    'hasReorderableColumns',
    'hasToggleableColumns',
    'reorderAnimationDuration' => 300,
])

<div
    @if ($hasToggleableColumns)
        {{-- The checkbox ids are scoped with `x-id` so they stay unique when multiple tables on the same page share column names, otherwise a label's `for` could activate a checkbox in another table's column manager. --}}
        x-id="['fi-ta-col-manager-group-checkbox', 'fi-ta-col-manager-column-checkbox']"
    @endif
    @if ($hasReorderableColumns)
        x-sortable
        x-on:end.stop="reorderColumns($event.target.sortable.toArray())"
        data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
    @endif
    {{
        (new FilamentComponentAttributeBag)
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
                        <label
                            @if ($hasToggleableColumns) x-bind:for="$id('fi-ta-col-manager-group-checkbox', column.name)" @endif
                            class="fi-ta-col-manager-label"
                        >
                            @if ($hasToggleableColumns)
                                <input
                                    type="checkbox"
                                    class="fi-checkbox-input fi-valid"
                                    x-bind:id="$id('fi-ta-col-manager-group-checkbox', column.name)"
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
                                x-bind:aria-label="@js(__('filament-tables::table.column_manager.actions.reorder.label')) + (column.name ? ' ' + column.name : '')"
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
                                        @if ($hasToggleableColumns) x-bind:for="$id('fi-ta-col-manager-column-checkbox', groupColumn.name)" @endif
                                        class="fi-ta-col-manager-label"
                                    >
                                        @if ($hasToggleableColumns)
                                            <input
                                                type="checkbox"
                                                class="fi-checkbox-input fi-valid"
                                                x-bind:id="$id('fi-ta-col-manager-column-checkbox', groupColumn.name)"
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
                                            x-bind:aria-label="@js(__('filament-tables::table.column_manager.actions.reorder.label')) + (groupColumn.name ? ' ' + groupColumn.name : '')"
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
                    <label
                        @if ($hasToggleableColumns) x-bind:for="$id('fi-ta-col-manager-column-checkbox', column.name)" @endif
                        class="fi-ta-col-manager-label"
                    >
                        @if ($hasToggleableColumns)
                            <input
                                type="checkbox"
                                class="fi-checkbox-input fi-valid"
                                x-bind:id="$id('fi-ta-col-manager-column-checkbox', column.name)"
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
                            x-bind:aria-label="@js(__('filament-tables::table.column_manager.actions.reorder.label')) + (column.name ? ' ' + column.name : '')"
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
