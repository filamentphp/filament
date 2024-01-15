<x-filament-panels::page.simple>
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.password-reset.reset.form.before') }}
    
    <x-filament-panels::form wire:submit="resetPassword">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.password-reset.reset.form.after') }}
</x-filament-panels::page.simple>
