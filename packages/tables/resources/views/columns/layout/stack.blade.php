@php
    use Filament\Support\Enums\Alignment;

    $alignment = $getAlignment() ?? Alignment::Start;

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'flex flex-col',
                match ($alignment) {
                    Alignment::Start => 'items-start',
                    Alignment::Center => 'items-center',
                    Alignment::End, Alignment::Right => 'items-end',
                    default => $alignment,
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
