@php
    use Filament\Tables\Actions\Position;
    use Filament\Tables\Filters\Layout;

    $actions = $getActions();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();
    $columns = $getColumns();
    $content = $getContent();
    $contentFooter = $getContentFooter();
    $filterIndicators = collect($getFilters())
        ->mapWithKeys(fn (\Filament\Tables\Filters\BaseFilter $filter): array => [$filter->getName() => $filter->getIndicators()])
        ->filter(fn (array $indicators): bool => count($indicators))
        ->all();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $heading = $getHeading();
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSelectionEnabled = $isSelectionEnabled();
    $isStriped = $isStriped();
    $hasFilters = $isFilterable();
    $hasFiltersPopover = $hasFilters && ($getFiltersLayout() === Layout::Popover);
    $hasFiltersAboveContent = $hasFilters && ($getFiltersLayout() === Layout::AboveContent);
    $hasFiltersBelowContent = $hasFilters && ($getFiltersLayout() === Layout::BelowContent);
    $isColumnToggleFormVisible = $hasToggleableColumns();
    $records = $getRecords();

    $columnsCount = count($columns);
    if (count($actions) && (! $isReordering)) $columnsCount++;
    if ($isSelectionEnabled || $isReordering) $columnsCount++;

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

            this.selectedRecords = await $wire.getAllTableRecordKeys()

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

        // https://github.com/laravel/framework/blob/5299c22321c0f1ea8ff770b84a6c6469c4d6edec/src/Illuminate/Translation/MessageSelector.php#L15
        pluralize: function (text, number, variables) {
            function extract(segments, number) {
                for (const part of segments) {
                    const line = extractFromString(part, number)

                    if (line !== null) {
                        return line
                    }
                }
            }

            function extractFromString(part, number) {
                const matches = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s)

                if (matches === null || matches.length !== 3) {
                    return null
                }

                const condition = matches[1]

                const value = matches[2]

                if (condition.includes(',')) {
                    const [from, to] = condition.split(',', 2)

                    if (to === '*' && number >= from) {
                        return value
                    } else if (from === '*' && number <= to) {
                        return value
                    } else if (number >= from && number <= to) {
                        return value
                    }
                }

                return condition == number ? value : null
            }

            function ucfirst(string) {
                return string.toString().charAt(0).toUpperCase() + string.toString().slice(1)
            }

            function replace(line, replace) {
                if (replace.length === 0) {
                    return line
                }

                const shouldReplace = {}

                for (let [key, value] of Object.entries(replace)) {
                    shouldReplace[':' + ucfirst(key ?? '')] = ucfirst(value ?? '')
                    shouldReplace[':' + key.toUpperCase()] = value.toString().toUpperCase()
                    shouldReplace[':' + key] = value
                }

                Object.entries(shouldReplace).forEach(([key, value]) => {
                    line = line.replaceAll(key, value)
                })

                return line
            }

            function stripConditions(segments) {
                return segments.map(part => part.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, ''))
            }

            let segments = text.split('|')

            const value = extract(segments, number)

            if (value !== null && value !== undefined) {
                return replace(value.trim(), variables)
            }

            segments = stripConditions(segments)

            return replace(segments.length > 1 && number > 1 ? segments[1] : segments[0], variables)
        }
    }"
    class="filament-tables-component"
>
    <x-tables::container>
        <div
            x-show="hasHeader = (@js($renderHeader = ($header || $heading || ($headerActions && (! $isReordering)) || $isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible)) || selectedRecords.length)"
            {!! ! $renderHeader ? 'x-cloak' : null !!}
        >
            @if ($header)
                {{ $header }}
            @elseif ($heading || $headerActions)
                <div @class([
                    'px-2 pt-2',
                    'hidden' => $isReordering,
                ])>
                    <x-tables::header :actions="$isReordering ? [] : $headerActions" class="mb-2">
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $getDescription() }}
                        </x-slot>
                    </x-tables::header>

                    <x-tables::hr :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible) . ' || selectedRecords.length'" />
                </div>
            @endif

            @if ($hasFiltersAboveContent)
                <div class="px-2 pt-2">
                    <div class="p-4 mb-2">
                        <x-tables::filters :form="$getFiltersForm()" />
                    </div>

                    <x-tables::hr :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $isColumnToggleFormVisible) . ' || selectedRecords.length'" />
                </div>
            @endif

            <div
                x-show="@js($shouldRenderHeaderDiv = ($isReorderable || $isGlobalSearchVisible || $hasFiltersPopover || $isColumnToggleFormVisible)) || selectedRecords.length"
                {!! ! $shouldRenderHeaderDiv ? 'x-cloak' : null !!}
                class="flex items-center justify-between p-2 h-14"
                x-bind:class="{
                    'gap-2': @js($isReorderable) || selectedRecords.length,
                }"
            >
                <div class="flex items-center gap-2">
                    @if ($isReorderable)
                        <x-tables::reorder.trigger
                            :enabled="$isReordering"
                        />
                    @endif

                    @if (! $isReordering)
                        <x-tables::bulk-actions
                            x-show="selectedRecords.length"
                            :actions="$getBulkActions()"
                        />
                    @endif
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersPopover || $isColumnToggleFormVisible)
                    <div class="w-full flex items-center justify-end gap-2 md:max-w-md">
                        @if ($isGlobalSearchVisible)
                            <div class="flex-1 flex items-center justify-end">
                                <x-tables::search-input />
                            </div>
                        @endif

                        @if ($hasFiltersPopover)
                            <x-tables::filters.popover
                                :form="$getFiltersForm()"
                                :width="$getFiltersFormWidth()"
                                :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                class="shrink-0"
                            />
                        @endif

                        @if ($isColumnToggleFormVisible)
                            <x-tables::toggleable
                                :form="$getColumnToggleForm()"
                                :width="$getColumnToggleFormWidth()"
                                class="shrink-0"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($isReordering)
            <x-tables::reorder.indicator
                :colspan="$columnsCount"
                :class="\Illuminate\Support\Arr::toCssClasses([
                    'border-t',
                    'dark:border-gray-700' => config('tables.dark_mode'),
                ])"
            />
        @elseif ($isSelectionEnabled)
            <x-tables::selection-indicator
                :all-records-count="$getAllRecordsCount()"
                :colspan="$columnsCount"
                x-show="selectedRecords.length"
                :class="\Illuminate\Support\Arr::toCssClasses([
                    'border-t',
                    'dark:border-gray-700' => config('tables.dark_mode'),
                ])"
            >
                <x-slot name="selectedRecordsCount">
                    <span x-text="selectedRecords.length"></span>
                </x-slot>
            </x-tables::selection-indicator>
        @endif

        <x-tables::filters.indicators
            :indicators="$filterIndicators"
            :class="\Illuminate\Support\Arr::toCssClasses([
                'border-t',
                'dark:border-gray-700' => config('tables.dark_mode'),
            ])"
        />

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
            @if ($content)
                {{ $content->with(['records' => $records]) }}
            @else
                <x-tables::table :poll="$getPollingInterval()">
                    <x-slot name="header">
                        @if ($isReordering)
                            <th></th>
                        @else
                            @if (count($actions) && $actionsPosition === Position::BeforeCells)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell alignment="right">
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled)
                                <x-tables::checkbox-cell>
                                    <x-slot
                                        name="checkbox"
                                        x-on:click="toggleSelectRecordsOnPage"
                                        x-bind:checked="
                                            let recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                    ></x-slot>
                                </x-tables::checkbox-cell>
                            @endif

                            @if (count($actions) && $actionsPosition === Position::BeforeColumns)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell alignment="right">
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif
                        @endif

                        @foreach ($columns as $column)
                            <x-tables::header-cell
                                :extra-attributes="$column->getExtraHeaderAttributes()"
                                :is-sort-column="$getSortColumn() === $column->getName()"
                                :name="$column->getName()"
                                :alignment="$column->getAlignment()"
                                :sortable="$column->isSortable() && (! $isReordering)"
                                :sort-direction="$getSortDirection()"
                                :class="$getHiddenClasses($column)"
                            >
                                {{ $column->getLabel() }}
                            </x-tables::header-cell>
                        @endforeach

                        @if (count($actions) && (! $isReordering) && $actionsPosition === Position::AfterCells)
                            @if ($actionsColumnLabel)
                                <x-tables::header-cell alignment="right">
                                    {{ $actionsColumnLabel }}
                                </x-tables::header-cell>
                            @else
                                <th class="w-5"></th>
                            @endif
                        @endif
                    </x-slot>

                    @if ($isColumnSearchVisible)
                        <x-tables::row>
                            @if ($isReordering)
                                <td></td>
                            @else
                                @if (count($actions) && in_array($actionsPosition, [Position::BeforeCells, Position::BeforeColumns]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled)
                                    <td></td>
                                @endif
                            @endif

                            @foreach ($columns as $column)
                                <x-tables::cell class="px-4 py-1">
                                    @if ($column->isIndividuallySearchable())
                                        <x-tables::search-input wire-model="tableColumnSearchQueries.{{ $column->getName() }}" />
                                    @endif
                                </x-tables::cell>
                            @endforeach

                            @if (count($actions) && (! $isReordering) && $actionsPosition === Position::AfterCells)
                                <td></td>
                            @endif
                        </x-tables::row>
                    @endif

                    @forelse ($records as $record)
                        @php
                            $recordAction = $getRecordAction($record);
                            $recordKey = $getRecordKey($record);
                            $recordUrl = $getRecordUrl($record);
                        @endphp

                        <x-tables::row
                            :record-action="$recordAction"
                            :record-url="$recordUrl"
                            :wire:key="$this->id . '.table.records.' . $recordKey"
                            :wire:sortable.item="$isReordering ? $recordKey : null"
                            :wire:sortable.handle="$isReordering"
                            :striped="$isStriped"
                            x-bind:class="{
                                'bg-gray-50 {{ config('tables.dark_mode') ? 'dark:bg-gray-500/10' : '' }}': isRecordSelected('{{ $recordKey }}'),
                            }"
                            :class="\Illuminate\Support\Arr::toCssClasses(array_merge(
                                [
                                    'group cursor-move' => $isReordering,
                                ],
                                $getRecordClasses($record),
                            ))"
                        >
                            @if ($isReordering)
                                <x-tables::reorder.cell />
                            @else
                                @if (count($actions) && $actionsPosition === Position::BeforeCells)
                                    <x-tables::actions-cell
                                        :actions="$actions"
                                        :record="$record"
                                    />
                                @endif

                                @if ($isSelectionEnabled)
                                    <x-tables::checkbox-cell>
                                        <x-slot
                                            name="checkbox"
                                            x-model="selectedRecords"
                                            :value="$recordKey"
                                            class="table-row-checkbox"
                                        ></x-slot>
                                    </x-tables::checkbox-cell>
                                @endif

                                @if (count($actions) && $actionsPosition === Position::BeforeColumns)
                                    <x-tables::actions-cell
                                        :actions="$actions"
                                        :record="$record"
                                    />
                                @endif
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
                                    :record-action="$recordAction"
                                    :record-url="$recordUrl"
                                    :should-open-url-in-new-tab="$column->shouldOpenUrlInNewTab()"
                                    :url="$column->getUrl()"
                                    :is-click-disabled="$column->isClickDisabled() || $isReordering"
                                    :class="$getHiddenClasses($column)"
                                    wire:loading.remove.delay
                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                >
                                    {{ $column }}
                                </x-tables::cell>
                            @endforeach

                            @if (count($actions) && (! $isReordering) && $actionsPosition === Position::AfterCells)
                                <x-tables::actions-cell
                                    :actions="$actions"
                                    :record="$record"
                                />
                            @endif

                            <x-tables::loading-cell
                                :colspan="$columnsCount"
                                wire:loading.class.remove.delay="hidden"
                                class="hidden"
                                wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                            />
                        </x-tables::row>
                    @empty
                        <tr><td colspan="{{ $columnsCount }}">
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
                        </td></tr>
                    @endforelse

                    @if ($contentFooter)
                        <x-slot name="footer">
                            {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                        </x-slot>
                    @endif
                </x-tables::table>
            @endif
        </div>

        @if (
            $records instanceof \Illuminate\Contracts\Pagination\Paginator &&
            ((! $records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || $records->total())
        )
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

        @if ($hasFiltersBelowContent)
            <div class="px-2 pb-2">
                <x-tables::hr />

                <div class="p-4 mt-2">
                    <x-tables::filters :form="$getFiltersForm()" />
                </div>
            </div>
        @endif
    </x-tables::container>

    <form wire:submit.prevent="callMountedTableAction">
        @php
            $action = $getMountedAction();
        @endphp

        <x-tables::modal
            :id="$this->id . '-table-action'"
            :wire:key="$action ? $this->id . '.table' . ($getMountedActionRecordKey() ? '.records.' . $getMountedActionRecordKey() : null) . '.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            display-classes="block"
        >
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

                        @if ($subheading = $action->getModalSubheading())
                            <x-tables::modal.subheading>
                                {{ $subheading }}
                            </x-tables::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if ($action->hasFormSchema())
                    {{ $getMountedActionForm() }}
                @endif

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-tables::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-tables::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-tables::modal>
    </form>

    <form wire:submit.prevent="callMountedTableBulkAction">
        @php
            $action = $getMountedBulkAction();
        @endphp

        <x-tables::modal
            :id="$this->id . '-table-bulk-action'"
            :wire:key="$action ? $this->id . '.table.bulk-actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            display-classes="block"
        >
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

                        @if ($subheading = $action->getModalSubheading())
                            <x-tables::modal.subheading>
                                {{ $subheading }}
                            </x-tables::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if ($action->hasFormSchema())
                    {{ $getMountedBulkActionForm() }}
                @endif

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-tables::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-tables::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-tables::modal>
    </form>

    @if (! $this instanceof \Filament\Tables\Contracts\RendersFormComponentActionModal)
        {{ $this->modal }}
    @endif
</div>
