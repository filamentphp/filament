<div 
    x-data="{ 
        type: '{{ session('notification.type') ?? 'info' }}', 
        message: '{{ session('notification.message') ?? '' }}', 
        open: {{ session()->has('notification') ? 'true' : 'false' }},
    }" 
    class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end"
    @filament-notification-notify.window="
        type = $event.detail.type; 
        message = $event.detail.message; 
        open = true;
    "
>
    <div  
        x-transition:enter="transform ease-out duration-300 transition" 
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
        x-transition:leave="transition ease-in duration-100" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        class="max-w-sm w-full pointer-events-auto shadow-xl rounded bg-gray-800 dark:bg-black text-gray-50 p-4 flex items-start"
        role="alert"
        x-show="open" 
    >
        <div class="flex-shrink-0">
            <template x-if="type === 'success'">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-500" />
            </template>
            <template x-if="type === 'error'">
                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-500" />
            </template>
            <template x-if="type === 'info'">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500" />
            </template>
        </div>
        <div class="ml-3 flex-grow mr-4 text-sm leading-5" x-html="message"></div>
        <div class="ml-4 flex-shrink-0 flex">
            <button @click.prevent="open = false" class="text-gray-500 hover:text-gray-300 transition ease-in-out duration-200">
                <x-heroicon-o-x class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.livewire.on('filament.notification.notify', data => {
            window.dispatchEvent(new CustomEvent('filament-notification-notify', { 
                detail: { 
                    type: data.type, 
                    message: data.message,
                }
            }));
        })
    </script>
@endpush