<x-filament-panels::page.simple>
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Support\View\RenderHook::PANELS_AUTH_PASSWORD_RESET_RESET_FORM_BEFORE) }}

    <x-filament-panels::form wire:submit="resetPassword">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Support\View\RenderHook::PANELS_AUTH_PASSWORD_RESET_RESET_FORM_AFTER) }}
</x-filament-panels::page.simple>
