<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'flex',
                match ($getFromBreakpoint()) {
                    'sm' => 'flex-col gap-1 sm:flex-row sm:items-center sm:gap-3',
                    'md' => 'flex-col gap-1 md:flex-row md:items-center md:gap-3',
                    'lg' => 'flex-col gap-1 lg:flex-row lg:items-center lg:gap-3',
                    'xl' => 'flex-col gap-1 xl:flex-row xl:items-center xl:gap-3',
                    '2xl' => 'flex-col gap-1 2xl:flex-row 2xl:items-center 2xl:gap-3',
                    default => 'items-center gap-3',
                },
            ])
    }}
>
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
        :row-loop="$getRowLoop()"
    />
</div>
