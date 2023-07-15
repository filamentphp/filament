<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'flex flex-col',
                match ($getAlignment()) {
                    'center' => 'items-center',
                    'end', 'right' => 'items-end',
                    'start', 'left', null => 'items-start',
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
