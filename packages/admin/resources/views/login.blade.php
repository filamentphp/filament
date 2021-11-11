<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="p-2 max-w-md space-y-8 w-screen">
        <form wire:submit.prevent="submit" class="bg-white space-y-8 shadow border border-gray-300 rounded-2xl p-8">
            <h2 class="font-bold tracking-tight text-center text-gray-900 text-2xl">
                {{ __(__('filament::login.heading')) }}
            </h2>

            {{ $this->form }}

            <x-filament::button type="submit" class="w-full">
                {{ __(__('filament::login.buttons.submit.label')) }}
            </x-filament::button>
        </form>

        <x-filament::footer />
    </div>
</div>
