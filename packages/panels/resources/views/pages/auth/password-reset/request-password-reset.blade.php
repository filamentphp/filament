<div>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ $this->loginAction }}
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
