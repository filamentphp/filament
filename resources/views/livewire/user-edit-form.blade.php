<div class="grid grid-cols-1 md:grid-cols-3 md:gap-4 lg:gap-8">

    <form wire:submit.prevent="update" class="col-span-2">

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
                    <x-filament-checkbox name="is_super_admin" label="Super admin?" wire:model="is_super_admin" />
                    <x-slot name="info">
                        Super admins have access to all of {{ config('app.name') }}.
                    </x-slot>
                </x-filament-input-group>
            
                @if (count($roles) && !$is_super_admin)
                    <fieldset>
                        <legend>Roles</legend>
                        <ol>
                            @foreach($roles as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </ol>
                    </fieldset>
                @endif

            </x-filament-tab>

            <x-filament-button wire:key="input-submit" label="Update" />

        </x-filament-tabs>
    
    </form>

</div>