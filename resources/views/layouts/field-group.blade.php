<div class="space-y-2">
    @if ($field->label || $field->hint)
        <div class="flex items-center justify-between space-x-2">
            @if ($field->label)
                <x-filament::label :for="$field->id">
                    {{ __($field->label) }}

                    @if ($field->required)
                        <sup class="font-medium text-red-700">*</sup>
                    @endif
                </x-filament::label>
            @endif

            @if ($field->hint)
                <x-filament::hint>
                    @markdown(__($field->hint))
                </x-filament::hint>
            @endif
        </div>
    @endif

    @yield('field')

    <x-filament::error :field="$field->errorKey" />

    @if ($field->help)
        <x-filament::help>
            @markdown(__($field->help))
        </x-filament::help>
    @endif
</div>
