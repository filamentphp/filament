@extends('filament::layouts.admin')

@section('title', $title)

@section('main')

    <div class="grid grid-cols-1 md:grid-cols-7 gap-4 lg:gap-8">

        <x-filament-tabs tab="account" :tabs="['account' => 'Account', 'permissions' => 'Permissions']" class="md:col-span-5">

            <x-filament-tab id="account">

                @livewire('filament::user-edit', [
                    'model' => $user, 
                    'goback' => 'alpine.admin.users.index'
                ])

            </x-filament-tab>
        
            <x-filament-tab id="permissions">
                
                Permissions...

            </x-filament-tab>

        </x-filament-tabs>

        <x-filament-well class="md:col-span-2">

            @livewire('filament::user-meta', [
                'user' => $user,
            ])

        </x-filament-well>

    </div>

@endsection