<form wire:submit.prevent="resetPassword" class="space-y-4">
    <x-filament::input-group-stacked name="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" name="email" wire:model.defer="email" id="email" required autocomplete="email" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked name="password" :label="__('New Password')" required>   
        <x-filament::input type="password" name="password" wire:model.defer="password" id="password" required autocomplete="new-password" autofocus />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked name="password_confirmation" :label="__('Confirm New Password')" required>   
        <x-filament::input type="password" name="password_confirmation" wire:model.defer="password_confirmation" id="password_confirmation" required autocomplete="new-password" />
    </x-filament::input-group-stacked>

    <x-filament::button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />
        {{ __('Reset Password') }}
    </x-filament::button>
</form>