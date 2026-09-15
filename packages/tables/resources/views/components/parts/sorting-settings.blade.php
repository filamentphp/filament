@php
    use Filament\Tables\Columns\Column;

    $sortableColumns = array_filter(
        $table->getVisibleColumns(),
        fn (Column $column): bool => $column->isSortable(),
    );
@endphp

@if ($table->hasContentLayout() && count($sortableColumns) && (! $table->isReordering()))
    <div
        x-data="{
            sort: $wire.$entangle('tableSort', true),
            column: null,
            direction: null,
        }"
        x-init="
            if (sort) {
                ;[column, direction] = sort.split(':')
                direction ??= 'asc'
            }

            $watch('sort', function () {
                if (! sort) {
                    return
                }

                ;[column, direction] = sort.split(':')
                direction ??= 'asc'
            })

            $watch('direction', function () {
                sort = column ? `${column}:${direction}` : null
            })

            $watch('column', function (newColumn, oldColumn) {
                if (! newColumn) {
                    direction = null
                    sort = column ? `${column}:${direction}` : null

                    return
                }

                if (oldColumn) {
                    sort = column ? `${column}:${direction}` : null

                    return
                }

                direction = 'asc'
                sort = column ? `${column}:${direction}` : null
            })
        "
        class="fi-ta-sorting-settings"
    >
        <label>
            <x-filament::input.wrapper
                :prefix="__('filament-tables::table.sorting.fields.column.label')"
            >
                <x-filament::input.select x-model="column">
                    <option value="">
                        {{ $table->getDefaultSortOptionLabel() }}
                    </option>

                    @foreach ($sortableColumns as $column)
                        <option value="{{ $column->getName() }}">
                            {{ $column->getLabel() }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>

        <label x-cloak x-show="column">
            <span class="fi-sr-only">
                {{ __('filament-tables::table.sorting.fields.direction.label') }}
            </span>

            <x-filament::input.wrapper>
                <x-filament::input.select x-model="direction">
                    <option value="asc">
                        {{ __('filament-tables::table.sorting.fields.direction.options.asc') }}
                    </option>

                    <option value="desc">
                        {{ __('filament-tables::table.sorting.fields.direction.options.desc') }}
                    </option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>
    </div>
@endif
