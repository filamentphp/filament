@php
    use Filament\Support\Enums\Alignment;
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'flex flex-col',
                match ($getAlignment()) {
                    Alignment::Center, 'center' => 'items-center',
                    Alignment::End, Alignment::Right, 'end', 'right' => 'items-end',
                    default => 'items-start',
                },
                match ($space = $getSpace()) {
                    1 => 'space-y-1',
                    2 => 'space-y-2',
                    3 => 'space-y-3',
                    default => $space,
                },
            ])
    }}
>
    <x-filament-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
        :row-loop="$getRowLoop()"
    />
</div>
