<x-filament::layouts.card>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ $this->loginAction }}
        </x-slot>
    @endif

    <form wire:submit="request" class="grid gap-y-6">
        {{ $this->form }}

        {{ $this->requestAction }}
    </form>
</x-filament::layouts.card>
