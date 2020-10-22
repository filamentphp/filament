<form wire:submit.prevent="login" class="flex flex-col space-y-4">
    <x-filament::input-group-stacked :name="$this->username()" :label="$this->label()" required>
        <x-filament::input :type="$this->type()" :name="$this->username()" :wire:model.defer="$this->username()" :id="$this->username()" required :autocomplete="$this->username()" />
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