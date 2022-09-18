<div class="space-y-1">
    <x-tables::columns-layout
        :components="$getComponents()"
        :record="$getRecord()"
        :record-key="$recordKey"
    />
</div>
