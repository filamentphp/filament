<div @class([
    'flex gap-1',
    match ($getFromBreakpoint()) {
        'sm' => 'flex-col sm:gap-3 sm:items-center sm:flex-row',
        'md' => 'flex-col md:gap-3 md:items-center md:flex-row',
        'lg' => 'flex-col lg:gap-3 lg:items-center lg:flex-row',
        'xl' => 'flex-col xl:gap-3 xl:items-center xl:flex-row',
        '2xl' => 'flex-col 2xl:gap-3 2xl:items-center 2xl:flex-row',
        default => 'flex-col items-center',
    },
])>
    <x-tables::columns-layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
