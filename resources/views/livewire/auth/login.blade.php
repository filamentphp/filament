<x-filament::form submit="login">
    <x-filament::input-group field="email" for="email" :label="__('E-Mail Address')" required>
        <x-filament::input type="email" field="email" wire:model.defer="email" id="email" required autocomplete="email" autofocus tabindex="1" />
        @if (Route::has('filament.register'))
            <x-slot name="hint">
                <a href="{{ route('filament.register') }}" tabindex="6">
                    {{ __('filament::auth.register') }}
                </a>
            </x-slot>
        @endif
    </x-filament::input-group>

    <x-filament::input-group field="password" for="password" :label="__('Password')" required>   
        <x-slot name="hint">
            <a href="{{ route('filament.password.forgot') }}" tabindex="5">
                {{ __('Forgot Your Password?') }}
            </a>
        </x-slot>
        <x-filament::input type="password" field="password" wire:model.defer="password" id="password" required autocomplete="current-password" tabindex="2" />
    </x-filament::input-group>

    <x-filament::checkbox field="remember" wire:model.defer="remember" tabindex="3">
        {{ __('Remember Me') }}
    </x-filament::checkbox>

    <x-filament::button type="submit" class="btn-primary w-full" wire:loading.attr="disabled" tabindex="4">
        <x-filament::loader class="w-6 h-6 absolute left-0 ml-2 pointer-events-none" wire:loading />
        {{ __('Login') }}
    </x-filament::button>

    <x-filament::message />
</x-filament::form>