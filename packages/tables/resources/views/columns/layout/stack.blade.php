<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'flex flex-col',
                match ($getAlignment()) {
                    'center' => 'items-center',
                    'right' => 'items-end',
                    default => 'items-start',
                },
                match ($getSpace()) {
                    1 => 'space-y-1',
                    2 => 'space-y-2',
                    3 => 'space-y-3',
                    default => null,
                },
            ])
    }}
>
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
