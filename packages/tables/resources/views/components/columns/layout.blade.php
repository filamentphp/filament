@props([
    'components',
    'record',
    'recordKey' => null,
    'rowLoop' => null,
])

@php
    $getHiddenClasses = function (Filament\Tables\Columns\Column | Filament\Tables\Columns\Layout\Component $layoutComponent): ?string {
        if ($breakpoint = $layoutComponent->getHiddenFrom()) {
            return match ($breakpoint) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
            };
        }

        if ($breakpoint = $layoutComponent->getVisibleFrom()) {
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

@foreach ($components as $layoutComponent)
    @php
        $layoutComponent->record($record);
        $layoutComponent->rowLoop($rowLoop);

        $isColumn = $layoutComponent instanceof \Filament\Tables\Columns\Column;
    @endphp

    @if ($layoutComponent->isVisible())
        <x-filament::grid.column
            :default="$layoutComponent->getColumnSpan('default')"
            :sm="$layoutComponent->getColumnSpan('sm')"
            :md="$layoutComponent->getColumnSpan('md')"
            :lg="$layoutComponent->getColumnSpan('lg')"
            :xl="$layoutComponent->getColumnSpan('xl')"
            :twoXl="$layoutComponent->getColumnSpan('2xl')"
            :defaultStart="$layoutComponent->getColumnStart('default')"
            :smStart="$layoutComponent->getColumnStart('sm')"
            :mdStart="$layoutComponent->getColumnStart('md')"
            :lgStart="$layoutComponent->getColumnStart('lg')"
            :xlStart="$layoutComponent->getColumnStart('xl')"
            :twoXlStart="$layoutComponent->getColumnStart('2xl')"
            @class([
                'flex-1 w-full' => $layoutComponent->canGrow(default: true),
                $getHiddenClasses($layoutComponent),
            ])
        >
            @if ($isColumn)
                <x-filament-tables::columns.column
                    :column="$layoutComponent->inline()"
                    :record="$record"
                    :record-key="$recordKey"
                />
            @else
                {{ $layoutComponent->viewData(['recordKey' => $recordKey]) }}
            @endif
        </x-filament::grid.column>
    @endif
@endforeach
