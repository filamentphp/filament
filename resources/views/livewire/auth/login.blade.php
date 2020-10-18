<form wire:submit.prevent="login" class="space-y-4" novalidate>
    <x-filament::input-group-stacked name="email" :label="__('E-Mail Address')" hideLabel required>
        <x-filament::input type="email" name="email" wire:model.defer="email" id="email" :placeholder="__('E-Mail Address')" required autocomplete="email" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked name="password" :label="__('Password')" hideLabel required>
        <x-filament::input type="password" name="password" wire:model.defer="password" id="password" :placeholder="__('Password')" required autocomplete="current-password" />
    </x-filament::input-group-stacked>

    <x-filament::button type="submit">
        {{ __('Login') }}
    </x-filament::button>
</form>