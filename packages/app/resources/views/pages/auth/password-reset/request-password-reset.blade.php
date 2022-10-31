<div>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            <x-filament::link :href="filament()->getLoginUrl()">
                <span class="rtl:hidden">&larr;</span>
                <span class="hidden rtl:inline">&rarr;</span>

                <span>
                    {{ __('filament::pages/auth/password-reset/request-password-reset.buttons.login.label') }}
                </span>
            </x-filament::link>
        </x-slot>
    @endif

    <form wire:submit.prevent="request" class="space-y-8">
        {{ $this->form }}

        <x-filament::button type="submit" form="request" class="w-full">
            {{ __('filament::pages/auth/password-reset/request-password-reset.buttons.request.label') }}
        </x-filament::button>
    </form>
</div>
