@props([
    'columns' => null,
    'hasReorderableColumns',
    'hasToggleableColumns',
    'reorderAnimationDuration' => 300,
])

@php
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Tables\View\TablesIconAlias;
@endphp

<div
    @if ($hasToggleableColumns)
        {{-- The checkbox ids are scoped with `x-id` so they stay unique when multiple tables on the same page share column names, otherwise a label's `for` could activate a checkbox in another table's column manager. --}}
        x-id="['fi-ta-col-manager-group-checkbox', 'fi-ta-col-manager-column-checkbox']"
    @endif
    @if ($hasReorderableColumns)
        x-sortable
        x-on:end.stop="reorderColumns($event.target.sortable.toArray())"
        x-on:focusin.window="focusRevision++"
        x-on:pointerdown.window="focusRevision++"
        data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
    @endif
    {{
        (new FilamentComponentAttributeBag)
            ->grid($columns, GridDirection::Column)
            ->class(['fi-ta-col-manager-items'])
    }}
>
    <template
        x-for="column in getVisibleColumns()"
        x-bind:key="JSON.stringify([column.type, column.name])"
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
                            <div
                                x-data="{}"
                                x-id="['column-manager-position']"
                                class="fi-ta-col-manager-reorder-controls"
                            >
                                <span
                                    x-bind:id="$id('column-manager-position')"
                                    x-text="
                                        @js(__('filament-tables::table.column_manager.position')).replace(':position', getColumnPosition(column)).replace(
                                            ':count',
                                            getVisibleColumns().length,
                                        )
                                    "
                                ></span>

                                @foreach (['up', 'down'] as $direction)
                                    <button
                                        type="button"
                                        tabindex="-1"
                                        x-text="@js(__('filament-tables::table.column_manager.actions.move_' . $direction . '.label')).replace(':column', getColumnLabel(column))"
                                        x-bind:aria-describedby="$id('column-manager-position')"
                                        x-bind:disabled="! canMoveColumn(column, @js($direction))"
                                        x-bind:aria-disabled="isLoading || ! canMoveColumn(column, @js($direction))"
                                        x-bind:data-testid="'column-manager-' + column.name + '-move-{{ $direction }}'"
                                        x-on:click.stop="
                                            moveColumn(
                                                column,
                                                @js($direction),
                                                null,
                                                $event.currentTarget,
                                                isLive ? @js(__('filament-tables::table.column_manager.reordered')) : @js(__('filament-tables::table.column_manager.reordered_deferred')),
                                            )
                                        "
                                    ></button>
                                @endforeach
                            </div>

                            <button
                                x-sortable-handle
                                aria-hidden="true"
                                tabindex="-1"
                                x-on:click.stop
                                class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                type="button"
                            >
                                {{ \Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE) }}
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
                            x-for="groupColumn in getVisibleColumns(column.name)"
                            x-bind:key="JSON.stringify([groupColumn.type, groupColumn.name])"
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
                                        <div
                                            x-data="{}"
                                            x-id="['column-manager-position']"
                                            class="fi-ta-col-manager-reorder-controls"
                                        >
                                            <span
                                                x-bind:id="$id('column-manager-position')"
                                                x-text="
                                                    @js(__('filament-tables::table.column_manager.position_in_group')).replace(':position', getColumnPosition(groupColumn, column.name))
                                                        .replace(':count', getVisibleColumns(column.name).length)
                                                        .replace(':group', getColumnLabel(column))
                                                "
                                            ></span>

                                            @foreach (['up', 'down'] as $direction)
                                                <button
                                                    type="button"
                                                    tabindex="-1"
                                                    x-text="@js(__('filament-tables::table.column_manager.actions.move_' . $direction . '.label')).replace(':column', getColumnLabel(groupColumn))"
                                                    x-bind:aria-describedby="$id('column-manager-position')"
                                                    x-bind:disabled="! canMoveColumn(groupColumn, @js($direction), column.name)"
                                                    x-bind:aria-disabled="isLoading || ! canMoveColumn(groupColumn, @js($direction), column.name)"
                                                    x-bind:data-testid="'column-manager-' + groupColumn.name + '-move-{{ $direction }}'"
                                                    x-on:click.stop="
                                                        moveColumn(
                                                            groupColumn,
                                                            @js($direction),
                                                            column.name,
                                                            $event.currentTarget,
                                                            isLive ? @js(__('filament-tables::table.column_manager.reordered_in_group')) : @js(__('filament-tables::table.column_manager.reordered_in_group_deferred')),
                                                        )
                                                    "
                                                ></button>
                                            @endforeach
                                        </div>

                                        <button
                                            x-sortable-handle
                                            aria-hidden="true"
                                            tabindex="-1"
                                            x-on:click.stop
                                            class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                                            type="button"
                                        >
                                            {{ \Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE) }}
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
                        <div
                            x-data="{}"
                            x-id="['column-manager-position']"
                            class="fi-ta-col-manager-reorder-controls"
                        >
                            <span
                                x-bind:id="$id('column-manager-position')"
                                x-text="
                                    @js(__('filament-tables::table.column_manager.position')).replace(':position', getColumnPosition(column)).replace(
                                        ':count',
                                        getVisibleColumns().length,
                                    )
                                "
                            ></span>

                            @foreach (['up', 'down'] as $direction)
                                <button
                                    type="button"
                                    tabindex="-1"
                                    x-text="@js(__('filament-tables::table.column_manager.actions.move_' . $direction . '.label')).replace(':column', getColumnLabel(column))"
                                    x-bind:aria-describedby="$id('column-manager-position')"
                                    x-bind:disabled="! canMoveColumn(column, @js($direction))"
                                    x-bind:aria-disabled="isLoading || ! canMoveColumn(column, @js($direction))"
                                    x-bind:data-testid="'column-manager-' + column.name + '-move-{{ $direction }}'"
                                    x-on:click.stop="
                                        moveColumn(
                                            column,
                                            @js($direction),
                                            null,
                                            $event.currentTarget,
                                            isLive ? @js(__('filament-tables::table.column_manager.reordered')) : @js(__('filament-tables::table.column_manager.reordered_deferred')),
                                        )
                                    "
                                ></button>
                            @endforeach
                        </div>

                        <button
                            x-sortable-handle
                            aria-hidden="true"
                            tabindex="-1"
                            x-on:click.stop
                            class="fi-ta-col-manager-reorder-handle fi-icon-btn"
                            type="button"
                        >
                            {{ \Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE) }}
                        </button>
                    @endif
                </div>
            </template>
        </div>
    </template>

    @if ($hasReorderableColumns)
        <p
            role="status"
            aria-live="polite"
            aria-atomic="true"
            class="fi-ta-col-manager-status"
            x-text="reorderAnnouncement"
        ></p>
    @endif
</div>
