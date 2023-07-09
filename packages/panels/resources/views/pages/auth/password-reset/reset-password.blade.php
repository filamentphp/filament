<x-filament::layouts.card>
    <form wire:submit="resetPassword" class="grid gap-y-8">
        {{ $this->form }}

        {{ $this->resetPasswordAction }}
    </form>
</x-filament::layouts.card>
