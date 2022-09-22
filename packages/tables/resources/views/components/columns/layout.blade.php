@props([
    'components',
    'record',
    'recordKey' => null,
])

@php
    $getHiddenClasses = function (\Filament\Tables\Columns\Column | \Filament\Tables\Columns\Layout\Component $layoutComponent): ?string {
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

        $isColumn = $layoutComponent instanceof \Filament\Tables\Columns\Column;
    @endphp

    @if (! $layoutComponent->isHidden())
        <div @class([
            'flex-1' => $isColumn ? $layoutComponent->canGrow() : true,
            $getHiddenClasses($layoutComponent),
        ])>
            @if ($isColumn)
                <x-tables::columns.column
                    :column="$layoutComponent->inline()"
                    :record="$record"
                    :record-key="$recordKey"
                />
            @else
                {{ $layoutComponent->viewData(['recordKey' => $recordKey]) }}
            @endif
        </div>
    @endif
@endforeach
