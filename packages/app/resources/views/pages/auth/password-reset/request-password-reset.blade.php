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

    <form
        wire:submit.prevent="request"
        class="grid gap-y-8"
    >
        {{ $this->form }}

        {{ $this->requestAction }}
    </form>
</div>
