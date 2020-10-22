<form wire:submit.prevent="login" class="space-y-4">
    <x-filament::input-group-stacked name="email" :label="__('filament::auth.labels.email')" required>
        <x-filament::input type="email" name="email" wire:model.defer="email" id="email" required autocomplete="email" autofocus />
    </x-filament::input-group-stacked>

    <x-filament::input-group-stacked name="password" :label="__('filament::auth.labels.password')" required>   
        @if (Route::has('filament.password.forgot'))
            <x-slot name="hint">
                <a href="{{ route('filament.password.forgot') }}" class="text-current hover:text-blue">
                    {{ __('Forgot Your Password?') }}
                </a>
            </x-slot>
        @endif
        <x-filament::input type="password" name="password" wire:model.defer="password" id="password" required autocomplete="current-password" />
    </x-filament::input-group-stacked>

    <x-filament::checkbox name="remember" wire:model.defer="remember">
        {{ __('Remember Me') }}
    </x-filament::checkbox>

    <x-filament::button type="submit" class="w-full">
        {{ __('Login') }}
    </x-filament::button>
</form>