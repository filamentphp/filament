<div>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/register.buttons.login.before') }}

            {{ $this->loginAction }}
        </x-slot>
    @endif

    <form wire:submit.prevent="register" class="grid gap-y-8">
        {{ $this->form }}

        {{ $this->registerAction }}
    </form>
</div>
