<div>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament::pages/auth/register.buttons.login.before') }} <x-filament::link :href="filament()->getLoginUrl()">
                {{ __('filament::pages/auth/register.buttons.login.label') }}
            </x-filament::link>
        </x-slot>
    @endif

    <form wire:submit.prevent="register" class="space-y-8">
        {{ $this->form }}

        <x-filament::button type="submit" form="register" class="w-full">
            {{ __('filament::pages/auth/register.buttons.register.label') }}
        </x-filament::button>
    </form>
</div>

