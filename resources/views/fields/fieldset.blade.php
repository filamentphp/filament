<fieldset class="border rounded border-grey-100 p-4 md:px-6">
    <legend class="text-sm leading-tight font-semibold cursor-pointer px-2">
        {{ __($legend) }}
    </legend>
    <x-filament::fields :fields="$fields" :class="$class" />
</fieldset>