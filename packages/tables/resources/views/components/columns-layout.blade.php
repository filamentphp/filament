@props([
    'components',
    'record',
    'recordKey' => null,
])

@php
    $getHiddenClasses = function (\Filament\Tables\Columns\Column | \Filament\Tables\Columns\Layout\Component $column): ?string {
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
                'sm' => 'hidden sm:block',
                'md' => 'hidden md:block',
                'lg' => 'hidden lg:block',
                'xl' => 'hidden xl:block',
                '2xl' => 'hidden 2xl:block',
            };
        }

        return null;
    };
@endphp

@foreach ($components as $column)
    @php
        $column->record($record);

        $isColumn = $column instanceof \Filament\Tables\Columns\Column;
    @endphp

    @if (! $column->isHidden())
        <div @class([
            'flex-1' => $isColumn ? $column->canGrow() : true,
            $getHiddenClasses($column),
        ])>
            @if ($isColumn)
                <x-tables::column-content
                    :column="$column->inline()"
                    :record="$record"
                    :record-key="$recordKey"
                />
            @else
                {{ $column->viewData(['recordKey' => $recordKey]) }}
            @endif
        </div>
    @endif
@endforeach
