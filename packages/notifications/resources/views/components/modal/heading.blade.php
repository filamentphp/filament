<x-filament-support::modal.heading
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :dark-mode="config('notifications.dark_mode')"
>
    {{ $slot }}
</x-filament-support::modal.heading>
