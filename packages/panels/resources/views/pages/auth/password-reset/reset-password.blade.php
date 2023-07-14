<x-filament::layouts.card>
    <form wire:submit="resetPassword" class="grid gap-y-6">
        {{ $this->form }}

        {{ $this->resetPasswordAction }}
    </form>
</x-filament::layouts.card>
