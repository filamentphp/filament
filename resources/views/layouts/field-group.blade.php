<div class="space-y-2">
    <div class="flex items-center justify-between space-x-2">
        @if ($label)
            <x-filament::label :for="$id ?? $model">
                {{ __($label) }}
            </x-filament::label>
        @endif
        @if ($hint)
            <x-filament::hint>
                @markdown($hint)
            </x-filament::hint>
        @endif
    </div>
    @yield('field')
    <x-filament::error :field="$model" />
    @if ($help)
        <x-filament::help>
            @markdown($help)
        </x-filament::help>
    @endif
</div>