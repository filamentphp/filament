<div @class([
    'items-center gap-x-3 gap-y-1',
    match ($getFromBreakpoint()) {
        'sm' => 'sm:flex',
        'md' => 'md:flex',
        'lg' => 'lg:flex',
        'xl' => 'xl:flex',
        '2xl' => '2xl:flex',
        default => 'flex',
    },
])>
    <x-tables::columns-layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
