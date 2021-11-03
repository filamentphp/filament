@php
    $actions = $getActions();
    $columns = $getColumns();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $heading = $getHeading();
    $isBulkActionsDropdownVisible = $isSelectionEnabled() && $getSelectedRecordCount();
    $isSearchVisible = $isSearchable();
    $isFiltersDropdownVisible = $isFilterable();

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

<div class="border border-gray-300 bg-white shadow rounded-xl overflow-hidden">
    @if ($header || $heading || $headerActions || $isBulkActionsDropdownVisible || $isSearchVisible || $isFiltersDropdownVisible)
        @if ($header)
            {{ $header }}
        @elseif ($heading || $headerActions)
            <div class="px-2 pt-2 space-y-2">
                <div class="px-4 py-2">
                    <div class="space-y-2 items-center justify-between md:flex md:space-y-0 md:space-x-2">
                        <div>
                            @if ($heading)
                                <h2 class="text-xl font-semibold tracking-tight">
                                    {{ $heading }}
                                </h2>
                            @endif

                            @if ($description = $getDescription())
                                <p class="text-gray-900">
                                    {{ $description }}
                                </p>
                            @endif
                        </div>

                        @if (count($headerActions))
                            <div class="flex items-center space-x-4 -mr-2">
                                @foreach ($headerActions as $action)
                                    {{ $action }}
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <x-tables::hr />
            </div>
        @endif

        <div class="divide-y">
            @if ($isBulkActionsDropdownVisible || $isSearchVisible || $isFiltersDropdownVisible)
                <div class="flex items-center justify-between p-2">
                    <div>
                        @if ($isBulkActionsDropdownVisible)
                            <div
                                x-data="{
                                    isOpen: false,
                                }"
                                x-cloak
                                class="relative mr-2"
                            >
                                <x-tables::icon-button
                                    icon="heroicon-o-dots-vertical"
                                    x-on:click="isOpen = ! isOpen"
                                    :label="__('tables::table.buttons.open_actions.label')"
                                />

                                <div
                                    x-show="isOpen"
                                    x-on:click.away="isOpen = false"
                                    x-transition:enter="transition"
                                    x-transition:enter-start="-translate-y-1 opacity-0"
                                    x-transition:enter-end="translate-y-0 opacity-100"
                                    x-transition:leave="transition"
                                    x-transition:leave-start="translate-y-0 opacity-100"
                                    x-transition:leave-end="-translate-y-1 opacity-0"
                                    class="absolute z-10 mt-2 shadow-xl rounded-xl w-52 top-full"
                                >
                                    <ul class="py-1 space-y-1 overflow-hidden bg-white shadow rounded-xl">
                                        @if (! $areAllRecordsSelected())
                                            <x-tables::dropdown.item wire:click="toggleSelectAllTableRecords" icon="heroicon-o-duplicate">
                                                {{ __('tables::table.actions.buttons.select_all.label', ['count' => $getAllRecordsCount()]) }}
                                            </x-tables::dropdown.item>

                                            <div aria-hidden="true" class="border-t border-gray-200 ml-11"></div>
                                        @endif

                                        @foreach($getBulkActions() as $bulkAction)
                                            <x-tables::dropdown.item
                                                :wire:click="'mountTableBulkAction(\'' . $bulkAction->getName() . '\')'"
                                                :icon="$bulkAction->getIcon()"
                                                :color="$bulkAction->getColor()"
                                            >
                                                {{ $bulkAction->getLabel() }}
                                            </x-tables::dropdown.item>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($isSearchVisible || $isFiltersDropdownVisible)
                        <div class="w-full md:w-auto flex items-center space-x-2 md:max-w-md">
                            @if ($isSearchVisible)
                                <div class="flex-1">
                                    <label for="tableSearchQueryInput" class="sr-only">
                                        {{ __('tables::table.fields.search_query.label') }}
                                    </label>

                                    <div class="relative group">
                                        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-9 h-9 text-gray-400 transition pointer-events-none group-focus-within:text-primary-500">
                                            <x-heroicon-o-search class="w-5 h-5" />
                                        </span>

                                        <input
                                            wire:model="tableSearchQuery"
                                            id="tableSearchQueryInput"
                                            placeholder="{{ __('tables::table.fields.search_query.placeholder') }}"
                                            type="search"
                                            class="block w-full h-9 pl-9 placeholder-gray-400 transition duration-75 border-gray-200 rounded-lg shadow-sm focus:border-primary-60 focus:ring-1 focus:ring-inset focus:ring-primary-600"
                                        >
                                    </div>
                                </div>
                            @endif

                            @if ($isFiltersDropdownVisible)
                                <div
                                    x-data="{ isOpen: false }"
                                    x-cloak
                                    class="flex-shrink-0 relative inline-block"
                                >
                                    <x-tables::icon-button
                                        icon="heroicon-o-filter"
                                        x-on:click="isOpen = ! isOpen"
                                        :label="__('tables::table.buttons.filter.label')"
                                    />

                                    <div
                                        x-show="isOpen"
                                        x-on:click.away="isOpen = false"
                                        x-transition:enter="transition ease duration-300"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease duration-300"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-2"
                                        class="absolute right-0 z-10 w-screen max-w-xs mt-2 shadow-xl top-full rounded-xl"
                                    >
                                        <div class="px-6 py-4 bg-white shadow rounded-xl">
                                            {{ $getFiltersForm() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <div class="overflow-y-auto">
            @if (($records = $getRecords())->count())
                <table class="w-full text-left divide-y table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            @if ($isSelectionEnabled())
                                <th class="w-4 px-4 whitespace-nowrap">
                                    <input
                                        {{ $areAllRecordsOnCurrentPageSelected() ? 'checked' : null }}
                                        wire:click="{{ $isPaginationEnabled() ? 'toggleSelectTableRecordsOnPage' : 'toggleSelectAllTableRecords' }}"
                                        type="checkbox"
                                        class="border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                    />
                                </th>
                            @endif

                            @foreach ($columns as $column)
                                <th @class([
                                    'px-4 py-2 text-sm font-semibold text-gray-600',
                                    $getHiddenClasses($column),
                                ])>
                                    <button
                                        @if ($column->isSortable())
                                            wire:click="sortTable('{{ $column->getName() }}')"
                                        @endif
                                        type="button"
                                        @class([
                                            'flex items-center space-x-1',
                                            'cursor-default' => ! $column->isSortable(),
                                        ])
                                    >
                                        <span>
                                            {{ $column->getLabel() }}
                                        </span>

                                        @if ($getSortColumn() === $column->getName())
                                            <span class="relative flex items-center">
                                                @if ($getSortDirection() === 'asc')
                                                    <x-heroicon-s-chevron-down class="w-3 h-3" />
                                                @elseif ($getSortDirection() === 'desc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3" />
                                                @endif
                                            </span>
                                        @endif
                                    </button>
                                </th>
                            @endforeach

                            @if (count($getActions()))
                                <th class="w-5">
                                    <span class="sr-only">
                                        Actions
                                    </span>
                                </th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y whitespace-nowrap">
                        @foreach ($records as $record)
                            <tr>
                                @if ($isSelectionEnabled())
                                    <td class="px-4 whitespace-nowrap">
                                        <input
                                            {{ $isRecordSelected($record->getKey()) ? 'checked' : null }}
                                            wire:click="toggleSelectTableRecord('{{ $record->getKey() }}')"
                                            type="checkbox"
                                            class="border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                    </td>
                                @endif

                                @foreach ($columns as $column)
                                    @php
                                        $column->record($record);
                                    @endphp

                                    <td class="{!! $getHiddenClasses($column) !!}">
                                        @if ($action = $column->getAction())
                                            <button
                                                @if (is_string($action))
                                                    wire:click="{{ $action }}('{{ $record->getKey() }}')"
                                                @elseif ($action instanceof \Closure)
                                                    wire:click="callTableColumnAction('{{ $column->getName() }}', '{{ $record->getKey() }}')"
                                                @endif
                                                type="button"
                                                class="block text-left"
                                            >
                                                {{ $column }}
                                            </button>
                                        @elseif ($url = $column->getUrl())
                                            <a
                                                href="{{ $url }}"
                                                {{ $column->shouldOpenUrlInNewTab() ? 'target="_blank"' : null }}
                                                class="block"
                                            >
                                                {{ $column }}
                                            </a>
                                        @else
                                            {{ $column }}
                                        @endif
                                    </td>
                                @endforeach

                                @if (count($actions))
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-2">
                                            @foreach ($actions as $action)
                                                {{ $action->record($record) }}
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                @if ($emptyState = $getEmptyState())
                    {{ $emptyState }}
                @else
                    <div class="flex items-center justify-center p-4">
                        <div class="flex flex-1 flex-col items-center justify-center max-w-md p-6 mx-auto space-y-6 text-center bg-white border rounded-2xl">
                            <div class="flex items-center justify-center w-16 h-16 text-blue-500 rounded-full bg-blue-50">
                                <x-dynamic-component :component="$getEmptyStateIcon()" class="w-6 h-6" />
                            </div>

                            <div class="max-w-xs space-y-1">
                                <h2 class="text-xl font-semibold tracking-tight">
                                    {{ $getEmptyStateHeading() }}
                                </h2>

                                @if ($description = $getEmptyStateDescription())
                                    <p class="text-sm font-medium text-gray-500">
                                        {{ $description }}
                                    </p>
                                @endif
                            </div>

                            @if ($actions = $getEmptyStateActions())
                                <div class="flex items-center justify-center space-x-4">
                                    @foreach ($actions as $action)
                                        {{ $action }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>

        @if ($isPaginationEnabled())
            <div class="p-2">
                <x-tables::pagination
                    :paginator="$records"
                    :records-per-page-select-options="$getRecordsPerPageSelectOptions()"
                />
            </div>
        @endif
    </div>
</div>

<form wire:submit.prevent="callMountedTableAction">
    <x-tables::modal id="action">
        @if ($action = $getMountedAction())
            <x-slot name="heading">
                {{ $action->getModalHeading() }}
            </x-slot>

            @if ($subheading = $action->getModalSubheading())
                <x-slot name="subheading">
                    {{ $subheading }}
                </x-slot>
            @endif

            @if ($action->hasFormSchema())
                {{ $getMountedActionForm() }}
            @endif

            <x-slot name="footer">
                <x-tables::modal.actions full-width>
                    <x-tables::button x-on:click="isOpen = false" color="secondary">
                        Cancel
                    </x-tables::button>

                    <x-tables::button type="submit" :color="$action->getColor()">
                        {{ $action->getModalButtonLabel() }}
                    </x-tables::button>
                </x-tables::modal.actions>
            </x-slot>
        @endif
    </x-tables::modal>
</form>

<form wire:submit.prevent="callMountedTableBulkAction">
    <x-tables::modal id="bulk-action">
        @if ($action = $getMountedBulkAction())
            <x-slot name="heading">
                {{ $action->getModalHeading() }}
            </x-slot>

            @if ($subheading = $action->getModalSubheading())
                <x-slot name="subheading">
                    {{ $subheading }}
                </x-slot>
            @endif

            @if ($action->hasFormSchema())
                {{ $getMountedBulkActionForm() }}
            @endif

            <x-slot name="footer">
                <x-tables::modal.actions full-width>
                    <x-tables::button x-on:click="isOpen = false" color="secondary">
                        Cancel
                    </x-tables::button>

                    <x-tables::button type="submit" :color="$action->getColor()">
                        {{ $action->getModalButtonLabel() }}
                    </x-tables::button>
                </x-tables::modal.actions>
            </x-slot>
        @endif
    </x-tables::modal>
</form>
