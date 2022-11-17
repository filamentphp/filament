<div {{ $attributes
    ->merge($getExtraAttributes(), escape: false)
    ->class(['px-4 py-3 bg-gray-100 rounded-lg dark:bg-gray-900'])
}}>
    <x-filament-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
