<fieldset class="rounded border border-gray-200 p-4 md:px-6">
    <legend class="text-sm leading-tight font-semibold px-2">
        {{ __($field->legend) }}
    </legend>

    <x-filament::fields :fields="$field->fields" :columns="$field->columns" />
</fieldset>
