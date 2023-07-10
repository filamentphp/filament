<x-filament::layouts.card>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/register.actions.login.before') }}

            {{ $this->loginAction }}
        </x-slot>
    @endif

    <form wire:submit="register" class="grid gap-y-8">
        {{ $this->form }}

        {{ $this->registerAction }}
    </form>
</x-filament::layouts.card>
