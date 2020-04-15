<div x-data="{ tab: '{{ $tab }}' }" {{ $attributes }}>
    <div role="tablist" class="rounded-t overflow-hidden bg-gray-100 dark:bg-gray-700 flex">
        @foreach ($tabs as $value => $label)
            <button 
                role="tab" 
                aria-controls="{{ $value }}-tab" 
                :tabindex="tab === '{{ $value }}' ? 0 : -1" 
                :aria-selected="tab === '{{ $value }}' ? 'true' : 'false'" 
                class="px-5 py-3 text-current text-sm leading-tight -mb-px opacity-50 hover:opacity-100 transition duration-200 ease-in-out" 
                :class="{ 'bg-white dark:bg-gray-800 opacity-100': tab === '{{ $value }}' }" 
                @click.prevent="tab = '{{ $value }}'"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
    <div class="p-5 rounded-b bg-white dark:bg-gray-800">
        {{ $slot }}
    </div>
</div>