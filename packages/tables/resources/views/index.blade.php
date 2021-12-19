@php
    $actions = $getActions();
    $columns = $getColumns();
    $contentFooter = $getContentFooter();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $heading = $getHeading();
    $isBulkActionsDropdownVisible = $isSelectionEnabled() && $getSelectedRecordsCount();
    $isSearchVisible = $isSearchable();
    $isFiltersDropdownVisible = $isFilterable();

    $columnsCount = count($columns);
    if ($isSelectionEnabled()) $columnsCount++;
    if (count($actions) > 0) $columnsCount++;

    $getHiddenClasses = function (\Filament\Tables\Columns\Column $column): ?string {
        if ($breakpoint = $column->getHiddenFrom()) {
            return match ($breakpoint) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
            };
        }

        if ($breakpoint = $column->getVisibleFrom()) {
            return match ($breakpoint) {
                'sm' => 'hidden sm:table-cell',
                'md' => 'hidden md:table-cell',
                'lg' => 'hidden lg:table-cell',
                'xl' => 'hidden xl:table-cell',
                '2xl' => 'hidden 2xl:table-cell',
            };
        }

        return null;
    };
@endphp

<div>
    <x-tables::container>
        @if ($hasTableHeader = ($header || $heading || $headerActions || $isBulkActionsDropdownVisible || $isSearchVisible || $isFiltersDropdownVisible))
            @if ($header)
                {{ $header }}
            @elseif ($heading || $headerActions)
                <div class="px-2 pt-2 space-y-2">
                    <x-tables::header :actions="$headerActions">
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $getDescription() }}
                        </x-slot>
                    </x-tables::header>

                    <x-tables::hr />
                </div>
            @endif

            <div class="divide-y">
                @if ($isBulkActionsDropdownVisible || $isSearchVisible || $isFiltersDropdownVisible)
                    <div class="flex items-center justify-between p-2 h-14">
                        <div>
                            @if ($isBulkActionsDropdownVisible)
                                <x-tables::bulk-actions
                                    :actions="$getBulkActions()"
                                    class="mr-2"
                                />
                            @endif
                        </div>

                        @if ($isSearchVisible || $isFiltersDropdownVisible)
                            <div class="w-full md:w-auto flex items-center gap-2 md:max-w-md">
                                @if ($isSearchVisible)
                                    <div class="flex-1">
                                        <x-tables::search-input />
                                    </div>
                                @endif

                                @if ($isFiltersDropdownVisible)
                                    <x-tables::filters
                                        :form="$getFiltersForm()"
                                        :width="$getFiltersFormWidth()"
                                        class="shrink-0"
                                    />
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <div @class([
            'overflow-y-auto',
            'rounded-t-xl' => ! $hasTableHeader,
            'border-t' => $hasTableHeader,
        ])>
            @if (($records = $getRecords())->count())
                <x-tables::table>
                    <x-slot name="header">
                        @if ($isSelectionEnabled())
                            <x-tables::checkbox-cell
                                :checked="$areAllRecordsOnCurrentPageSelected()"
                                :on-click="$isPaginationEnabled() ? 'toggleSelectTableRecordsOnPage' : 'toggleSelectAllTableRecords'"
                            />
                        @endif

                        @foreach ($columns as $column)
                            <x-tables::header-cell
                                :is-sort-column="$getSortColumn() === $column->getName()"
                                :name="$column->getName()"
                                :sortable="$column->isSortable()"
                                :sort-direction="$getSortDirection()"
                                :class="$getHiddenClasses($column)"
                            >
                                {{ $column->getLabel() }}
                            </x-tables::header-cell>
                        @endforeach

                        @if (count($actions))
                            <th class="w-5"></th>
                        @endif
                    </x-slot>

                    @if ($isSelectionEnabled() && $getSelectedRecordsCount() > 0)
                        <x-tables::selection-indicator
                            :all-records-count="$getAllRecordsCount()"
                            :are-all-records-on-current-page-selected="$areAllRecordsOnCurrentPageSelected()"
                            :colspan="$columnsCount"
                            :selected-records-count="$getSelectedRecordsCount()"
                        />
                    @endif

                    @foreach ($records as $record)
                        <x-tables::row wire:key="{{ $record->getKey() }}">
                            @if ($isSelectionEnabled())
                                <x-tables::checkbox-cell
                                    :checked="$isRecordSelected($record->getKey())"
                                    :on-click="'toggleSelectTableRecord(\'' . $record->getKey() . '\')'"
                                />
                            @endif

                            @foreach ($columns as $column)
                                @php
                                    $column->record($record);
                                @endphp

                                <x-tables::cell
                                    :action="$column->getAction()"
                                    :name="$column->getName()"
                                    :record="$record"
                                    :should-open-url-in-new-tab="$column->shouldOpenUrlInNewTab()"
                                    :url="$column->getUrl()"
                                    :class="$getHiddenClasses($column)"
                                >
                                    {{ $column }}
                                </x-tables::cell>
                            @endforeach

                            @if (count($actions))
                                <x-tables::actions-cell :actions="$actions" :record="$record" />
                            @endif
                        </x-tables::row>
                    @endforeach

                    @if ($contentFooter)
                        <x-slot name="footer">
                            {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                        </x-slot>
                    @endif

                </x-tables::table>
            @else
                @if ($emptyState = $getEmptyState())
                    {{ $emptyState }}
                @else
                    <div class="flex items-center justify-center p-4">
                        <x-tables::empty-state :icon="$getEmptyStateIcon()" :actions="$getEmptyStateActions()">
                            <x-slot name="heading">
                                {{ $getEmptyStateHeading() }}
                            </x-slot>

                            <x-slot name="description">
                                {{ $getEmptyStateDescription() }}
                            </x-slot>
                        </x-tables::empty-state>
                    </div>
                @endif
            @endif
        </div>

        @if ($isPaginationEnabled())
            <div class="p-2 border-t">
                <x-tables::pagination
                    :paginator="$records"
                    :records-per-page-select-options="$getRecordsPerPageSelectOptions()"
                />
            </div>
        @endif
    </x-tables::container>

    <form wire:submit.prevent="callMountedTableAction">
        @php
            $action = $getMountedAction();
        @endphp

        <x-tables::modal :id="\Illuminate\Support\Str::of(static::class)->replace('\\', '\\\\') . '-action'" :width="$action?->getModalWidth()" display-classes="block">
            @if ($action)
                @if ($action->isModalCentered())
                    <x-slot name="heading">
                        {{ $action->getModalHeading() }}
                    </x-slot>

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        <x-tables::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-tables::modal.heading>
                    </x-slot>
                @endif

                @if ($action->hasFormSchema())
                    {{ $getMountedActionForm() }}
                @endif

                <x-slot name="footer">
                    <x-tables::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-tables::modal.actions>
                </x-slot>
            @endif
        </x-tables::modal>
    </form>

    <form wire:submit.prevent="callMountedTableBulkAction">
        @php
            $action = $getMountedBulkAction();
        @endphp

        <x-tables::modal :id="\Illuminate\Support\Str::of(static::class)->replace('\\', '\\\\') . '-bulk-action'" :width="$action?->getModalWidth()" display-classes="block">
            @if ($action)
                @if ($action->isModalCentered())
                    <x-slot name="heading">
                        {{ $action->getModalHeading() }}
                    </x-slot>

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        <x-tables::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-tables::modal.heading>
                    </x-slot>
                @endif

                @if ($action->hasFormSchema())
                    {{ $getMountedBulkActionForm() }}
                @endif

                <x-slot name="footer">
                    <x-tables::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-tables::modal.actions>
                </x-slot>
            @endif
        </x-tables::modal>
    </form>
</div>
