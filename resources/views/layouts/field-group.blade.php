<div class="space-y-2">
    @if ($field->label || $field->hint)
        <div class="flex items-center justify-between space-x-2">
            @if ($field->label)
                <x-filament::label :for="$field->id">
                    {{ __($field->label) }}
                    @if (array_key_exists('required', $field->extraAttributes))
                        <sup class="text-lg font-medium text-red-700">*</sup>
                    @endif
                </x-filament::label>
            @endif

            @if ($field->hint)
                <x-filament::hint>
                    @markdown($field->hint)
                </x-filament::hint>
            @endif
        </div>
    @endif

    @yield('field')

    <x-filament::error :field="$field->errorKey ?? $field->name" />

    @if ($field->help)
        <x-filament::help>
            @markdown($field->help)
        </x-filament::help>
    @endif
</div>
