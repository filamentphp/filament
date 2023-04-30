<form
    wire:submit.prevent="resetPassword"
    class="grid gap-y-8"
>
    {{ $this->form }}

    {{ $this->resetPasswordAction }}
</form>
