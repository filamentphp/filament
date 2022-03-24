@php
    $actions = $getActions();
    $columns = $getColumns();
    $contentFooter = $getContentFooter();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $heading = $getHeading();
    $isSearchVisible = $isSearchable();
    $isFiltersDropdownVisible = $isFilterable();

    $columnsCount = count($columns);
    if (count($actions)) $columnsCount++;
    if ($isSelectionEnabled()) $columnsCount++;

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

<div
    x-data="{
        hasHeader: true,

        isLoading: false,

        selectedRecords: [],

        shouldCheckUniqueSelection: true,

        init: function () {
            $wire.on('deselectAllTableRecords', () => this.deselectAllRecords())

            $watch('selectedRecords', () => {
                if (! this.shouldCheckUniqueSelection) {
                    this.shouldCheckUniqueSelection = true

                    return
                }

                this.selectedRecords = [...new Set(this.selectedRecords)]

                this.shouldCheckUniqueSelection = false
            })
        },

        mountBulkAction: function (name) {
            $wire.mountTableBulkAction(name, this.selectedRecords)
        },

        toggleSelectRecordsOnPage: function () {
            let keys = this.getRecordsOnPage()

            if (this.areRecordsSelected(keys)) {
                this.deselectRecords(keys)

                return
            }

            this.selectRecords(keys)
        },

        getRecordsOnPage: function () {
            let keys = []

            for (checkbox of $el.getElementsByClassName('table-row-checkbox')) {
                keys.push(checkbox.value)
            }

            return keys
        },

        selectRecords: function (keys) {
            for (key of keys) {
                if (this.isRecordSelected(key)) {
                    continue
                }

                this.selectedRecords.push(key)
            }
        },

        deselectRecords: function (keys) {
            for (key of keys) {
                let index = this.selectedRecords.indexOf(key)

                if (index === -1) {
                    continue
                }

                this.selectedRecords.splice(index, 1)
            }
        },

        selectAllRecords: async function () {
            this.isLoading = true

            this.selectedRecords = (await $wire.getAllTableRecordKeys()).map((key) => key.toString())

            this.isLoading = false
        },

        deselectAllRecords: function () {
            this.selectedRecords = []
        },

        isRecordSelected: function (key) {
            return this.selectedRecords.includes(key)
        },

        areRecordsSelected: function (keys) {
            return keys.every(key => this.isRecordSelected(key))
        },
    }"
    class="filament-tables-component"
>
    <x-tables::container>
        <div
            x-show="hasHeader = ({{ ($renderHeader = ($header || $heading || $headerActions || $isSearchVisible || $isFiltersDropdownVisible)) ? 'true' : 'false' }} || selectedRecords.length)"
            {!! ! $renderHeader ? 'x-cloak' : null !!}
        >
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

                    <x-tables::hr x-show="{{ ($isSearchVisible || $isFiltersDropdownVisible) ? 'true' : 'false' }} || selectedRecords.length" />
                </div>
            @endif

            <div
                x-show="{{ ($renderHeaderDiv = ($isSearchVisible || $isFiltersDropdownVisible)) ? 'true' : 'false' }} || selectedRecords.length"
                {!! ! $renderHeaderDiv ? 'x-cloak' : null !!}
                class="flex items-center justify-between p-2 h-14"
            >
                <div>
                    <x-tables::bulk-actions
                        x-show="selectedRecords.length"
                        :actions="$getBulkActions()"
                        class="mr-2"
                    />
                </div>

                @if ($isSearchVisible || $isFiltersDropdownVisible)
                    <div class="w-full flex items-center justify-end gap-2 md:max-w-md">
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
        </div>

        <div
            @class([
                'overflow-y-auto relative',
                'dark:border-gray-700' => config('tables.dark_mode'),
                'rounded-t-xl' => ! $renderHeader,
                'border-t' => $renderHeader,
            ])
            x-bind:class="{
                'rounded-t-xl': ! hasHeader,
                'border-t': hasHeader,
            }"
        >
            @if (($records = $getRecords())->count())
                <x-tables::table>
                    <x-slot name="header">
                        @if ($isSelectionEnabled())
                            <x-tables::checkbox-cell>
                                <x-slot
                                    name="checkbox"
                                    x-on:click="toggleSelectRecordsOnPage"
                                    x-bind:checked="
                                        if (areRecordsSelected(getRecordsOnPage())) {
                                            $el.checked = true

                                            return 'checked'
                                        }

                                        $el.checked = false

                                        return null
                                    "
                                ></x-slot>
                            </x-tables::checkbox-cell>
                        @endif

                        @foreach ($columns as $column)
                            <x-tables::header-cell
                                :extra-attributes="$column->getExtraHeaderAttributes()"
                                :is-sort-column="$getSortColumn() === $column->getName()"
                                :name="$column->getName()"
                                :alignment="$column->getAlignment()"
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

                    @if ($isSelectionEnabled())
                        <x-tables::selection-indicator
                            :all-records-count="$getAllRecordsCount()"
                            :colspan="$columnsCount"
                            x-show="selectedRecords.length"
                        >
                            <x-slot name="selectedRecordsCount">
                                <span x-text="selectedRecords.length"></span>
                            </x-slot>
                        </x-tables::selection-indicator>
                    @endif

                    @foreach ($records as $record)
                        <x-tables::row
                            :record-url="$getRecordUrl($record)"
                            wire:key="{{ $record->getKey() }}"
                            x-bind:class="{
                                'bg-primary-500/10': isRecordSelected('{{ $record->getKey() }}'),
                            }"
                        >
                            @if ($isSelectionEnabled())
                                <x-tables::checkbox-cell>
                                    <x-slot
                                        name="checkbox"
                                        x-model="selectedRecords"
                                        :value="$record->getKey()"
                                        class="table-row-checkbox"
                                    ></x-slot>
                                </x-tables::checkbox-cell>
                            @endif

                            @foreach ($columns as $column)
                                @php
                                    $column->record($record);
                                @endphp

                                <x-tables::cell
                                    :action="$column->getAction()"
                                    :name="$column->getName()"
                                    :alignment="$column->getAlignment()"
                                    :record="$record"
                                    :tooltip="$column->getTooltip()"
                                    :record-action="$getRecordAction()"
                                    :record-url="$getRecordUrl($record)"
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
            <div @class([
                'p-2 border-t',
                'dark:border-gray-700' => config('tables.dark_mode'),
            ])>
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
