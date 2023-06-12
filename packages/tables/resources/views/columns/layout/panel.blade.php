<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'rounded-lg bg-gray-100 px-4 py-3',
                'dark:bg-gray-900' => config('forms.dark_mode'),
            ])
    }}
>
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
