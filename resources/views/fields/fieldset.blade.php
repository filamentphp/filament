<fieldset class="border rounded border-grey-50 p-4 md:px-6">
    <legend class="text-sm leading-tight font-semibold px-2">
        {{ __($field->legend) }}
    </legend>
    <div class="space-y-2">
        <x-filament::fields :fields="$field->fields" :class="$field->class" />
    </div>
</fieldset>