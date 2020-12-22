<fieldset class="border rounded border-grey-100 p-4 md:px-6">
    <legend class="text-sm leading-tight font-semibold px-2">
        {{ __($legend) }}
    </legend>
    <div class="space-y-2">
        <x-filament::fields :fields="$fields" :class="$class" />
        @if ($help)
            <x-filament::help>
                @markdown($help)
            </x-filament::help>
        @endif
    </div>
</fieldset>