<div>
    <form wire:submit.prevent="update" class="bg-white py-8 px-4 shadow rounded-md sm:px-10">

        <x-alpine-alert :type="session('alert.type')" :message="session('alert.message')" />
    
        <x-alpine-input-group wire:key="input-name" id="name" label="Name" required>
            <x-alpine-input id="name" name="name" wire:model="name" required />
        </x-alpine-input-group>
    
        <x-alpine-input-group wire:key="input-email" id="email" label="E-Mail Address" required>
            <x-alpine-input id="email" type="email" name="email" wire:model="email" required />
        </x-alpine-input-group>
        
        <x-alpine-button wire:key="input-submit" label="Update" />

    </form>
</div>