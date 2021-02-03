<fieldset @if ($field->legend) class="rounded border border-gray-200 p-4 md:px-6" @endif>
    @if ($field->legend)
        <legend class="text-sm leading-tight font-semibold px-2">
            {{ __($field->legend) }}
        </legend>
    @endif

    <x-filament::form embedded :fields="$field->fields" :columns="$field->columns" />
</fieldset>
