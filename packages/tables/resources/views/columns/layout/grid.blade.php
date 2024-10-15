<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->grid($columns = $getGridColumns())
            ->class([
                'fi-ta-grid',
                (($columns['default'] ?? 1) === 1) ? 'fi-gap-sm' : 'fi-gap-lg',
                ($columns['sm'] ?? null) ? (($columns['sm'] === 1) ? 'sm:fi-gap-sm' : 'sm:fi-gap-lg') : null,
                ($columns['md'] ?? null) ? (($columns['md'] === 1) ? 'md:fi-gap-sm' : 'md:fi-gap-lg') : null,
                ($columns['lg'] ?? null) ? (($columns['lg'] === 1) ? 'lg:fi-gap-sm' : 'lg:fi-gap-lg') : null,
                ($columns['xl'] ?? null) ? (($columns['xl'] === 1) ? 'xl:fi-gap-sm' : 'xl:fi-gap-lg') : null,
                ($columns['2xl'] ?? null) ? (($columns['2xl'] === 1) ? '2xl:fi-gap-sm' : '2xl:fi-gap-lg') : null,
            ])
    }}
>
    <x-filament-tables::columns.layout
        :components="$getComponents()"
        grid
        :record="$getRecord()"
        :record-key="$getRecordKey()"
    />
</div>
