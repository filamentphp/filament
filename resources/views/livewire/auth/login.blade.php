<form wire:submit.prevent="login" class="flex flex-col space-y-4" novalidate>
    <x-filament::input-group-stacked name="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" name="email" wire:model.defer="email" id="email" :placeholder="__('E-Mail Address')" required autocomplete="email" />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked name="password" :label="__('Password')" required>   
        @if (Route::has('filament.password.forgot'))
            <x-slot name="hint">
                <a href="{{ route('filament.password.forgot') }}" class="text-current hover:text-blue">
                    {{ __('Forgot Your Password?') }}
                </a>
            </x-slot>
        @endif
        <x-filament::input type="password" name="password" wire:model.defer="password" id="password" :placeholder="__('Password')" required autocomplete="current-password" />
    </x-filament::input-group-stacked>

    <x-filament::checkbox name="remember">
        {{ __('Remember Me') }}
    </x-filament::checkbox>

    <x-filament::button type="submit" class="w-full">
        {{ __('Login') }}
    </x-filament::button>
</form>