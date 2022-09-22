<div @class([
    'flex flex-col space-y-1',
    match ($getAlignment()) {
        'center' => 'items-center',
        'right' => 'items-end',
        default => 'items-start',
    },
])>
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
