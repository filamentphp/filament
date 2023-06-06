<div>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/login.buttons.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    <form wire:submit.prevent="authenticate" class="grid gap-y-8">
        {{ $this->form }}

        {{ $this->authenticateAction }}
    </form>
</div>
