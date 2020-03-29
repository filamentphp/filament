<div class="grid grid-cols-1 md:grid-cols-7 gap-4 lg:gap-8">

    <form wire:submit.prevent="save" class="md:col-span-5">

        <x-filament-tabs tab="account" :tabs="['account' => 'Account', 'permissions' => 'Permissions']">

            <x-filament-tab id="account">

                @include('filament::partials.fields', ['fields' => $fields, 'group' => 'account'])

            </x-filament-tab>
        
            <x-filament-tab id="permissions">
                
                @include('filament::partials.fields', ['fields' => $fields, 'group' => 'permissions'])

            </x-filament-tab>

            <x-filament-button label="Save">
                @if ($goback)
                    <x-slot name="dropdown">
                        <button wire:click.prevent="saveAndGoBack">{{ __('Save & Go Back') }}</button>
                    </x-slot>
                @endif
            </x-filament-button>

        </x-filament-tabs>

    </form>

    <x-filament-well class="md:col-span-2">

        <dl class="grid grid-cols-2 gap-2 md:gap-3 text-xs leading-tight">

            <dt class="text-gray-400">{{ __('filament::admin.created_at') }}</dt>
            <dd class="text-right">{{ $user->created_at->fromNow() }}</dd>
        
            <dt class="text-gray-400">{{ __('filament::admin.updated_at') }}</dt>
            <dd class="text-right">{{ $user->updated_at->fromNow() }}</dd>
        
            <dt class="text-gray-400">{{ __('filament::user.last_login_at') }}</dt>
            <dd class="text-right">{{ $user->last_login_at ? $user->last_login_at->fromNow() : __('filament::user.last_login_never') }}</dd>
        
            @if ($user->last_login_ip)
                <dt class="text-gray-400">{{ __('filament::user.last_login_ip') }}</dt>
                <dd class="text-right">{{ $user->last_login_ip }}</dd>
            @endif 
        
        </dl>

    </x-filament-well>

</div>
