@if ($message)
    <div   
        x-data="{ open: true }"
        x-show="open" 
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
        {{ $attributes->merge(['class' => 'flex justify-between items-start alert alert-'.$type]) }}
    >
        <div class="flex-grow mr-2">
            @markdown($message)
        </div>
        <button @click="open = false">
            <span aria-hidden="true">
                {{ Filament::svg('heroicons/outline-md/md-x', 'w-5 h-5')}}
            </span>
            <span class="sr-only">{{ __('Close') }}</span>
        </button>
    </div>
@endif
