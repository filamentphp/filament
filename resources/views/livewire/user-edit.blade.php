<div class="grid grid-cols-1 md:grid-cols-7 gap-4 lg:gap-8">

    <form wire:submit.prevent="update" class="md:col-span-5">

        <x-filament-tabs tab="account" :tabs="['account' => 'Account', 'permissions' => 'Permissions']">

            <x-filament-tab id="account">

                <x-filament-input-group wire:key="input-name" id="name" label="Name" required>
                    <x-filament-input id="name" name="name" wire:model="name" required />
                </x-filament-input-group>
            
                <x-filament-input-group wire:key="input-email" id="email" label="E-Mail Address" required>
                    <x-filament-input id="email" type="email" name="email" wire:model="email" required />
                </x-filament-input-group>

            </x-filament-tab>
        
            <x-filament-tab id="permissions">
              
                <x-filament-input-group>
                    <x-filament-checkbox name="is_super_admin" label="filament::permissions.super_admin" wire:model="is_super_admin" />
                    <x-slot name="info">
                        {{ __('filament::permissions.super_admin_info', ['name' => config('app.name')]) }}
                    </x-slot>
                </x-filament-input-group>
            
                @if (count($this->roles) && !$is_super_admin)
                    <fieldset class="mb-2">
                        <legend class="mb-2">Roles</legend>
                        <ol>
                            @foreach($this->roles as $role)
                                <li>
                                    <x-filament-checkbox 
                                        name="user_roles" 
                                        :label="ucfirst($role->name)" 
                                        :wire:model="'user_roles.'.$loop->index"
                                    />
                                </li>
                            @endforeach
                        </ol>
                    </fieldset>
                    <pre class="mb-4">{{ var_dump($user_roles) }}</pre>
                @endif

            </x-filament-tab>

            <x-filament-button wire:key="input-submit" label="Update" />

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