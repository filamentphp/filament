<fieldset @if ($field->label) class="rounded border border-gray-200 p-4 md:px-6" @endif>
    @if ($field->label)
        <legend class="text-sm leading-tight font-medium px-2">
            {{ __($field->label) }}
        </legend>
    @endif

    <x-filament::subform :fields="$field->fields" :columns="$field->columns" />
</fieldset>
