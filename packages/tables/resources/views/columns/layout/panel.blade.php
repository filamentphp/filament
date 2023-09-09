<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-panel rounded-lg bg-gray-50 p-4 ring-1 ring-inset ring-gray-950/5 dark:bg-white/5 dark:ring-white/10'])
    }}
>
    <x-filament-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
