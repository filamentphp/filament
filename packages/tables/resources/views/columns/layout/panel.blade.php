<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-panel'])
    }}
>
    <x-filament-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$getRecordKey()"
    />
</div>
