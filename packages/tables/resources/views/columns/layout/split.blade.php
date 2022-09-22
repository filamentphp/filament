<div @class([
    'flex',
    match ($getFromBreakpoint()) {
        'sm' => 'flex-col gap-1 sm:gap-3 sm:items-center sm:flex-row',
        'md' => 'flex-col gap-1 md:gap-3 md:items-center md:flex-row',
        'lg' => 'flex-col gap-1 lg:gap-3 lg:items-center lg:flex-row',
        'xl' => 'flex-col gap-1 xl:gap-3 xl:items-center xl:flex-row',
        '2xl' => 'flex-col gap-1 2xl:gap-3 2xl:items-center 2xl:flex-row',
        default => 'gap-3 items-center',
    },
])>
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
