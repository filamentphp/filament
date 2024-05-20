@props([
    'key',
    'page' => null,
    'title',
])

{{-- format-ignore-start --}}
<x-filament-tables::selection.checkbox
    :wire:key="$this->getId() . 'table.bulk_select_group.checkbox.' . $page"
    :label="__('filament-tables::table.fields.bulk_select_group.label', ['title' => $title])"
    :x-bind:checked="'
        const recordsInGroup = getRecordsInGroupOnPage(' . \Illuminate\Support\Js::from($key) . ')

        if (recordsInGroup.length && areRecordsSelected(recordsInGroup)) {
            $el.checked = true

            return \'checked\'
        }

        $el.checked = false

        return null
    '"
    :x-on:click="'toggleSelectRecordsInGroup(' . \Illuminate\Support\Js::from($key) . ')'"
/>
{{-- format-ignore-end --}}
