<div @class([
    'px-4 py-3 bg-gray-100 rounded-xl',
    'dark:bg-gray-900' => config('forms.dark_mode'),
])>
    <x-tables::columns.layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
