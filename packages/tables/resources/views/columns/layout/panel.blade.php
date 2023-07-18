<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['rounded-lg bg-gray-100 px-4 py-3 dark:bg-gray-900'])
    }}
>
    <x-filament-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
