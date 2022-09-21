<div @class([
    'flex flex-col space-y-1',
    match ($getAlignment()) {
        'left' => 'items-start',
        'center' => 'items-center',
        'right' => 'items-end',
        default => null,
    },
])>
    <x-tables::columns-layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
