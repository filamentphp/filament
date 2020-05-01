<div x-data="{ tab: '{{ $tabs[0] }}' }" {{ $attributes }}>
    <div role="tablist" class="rounded-t shadow overflow-hidden bg-gray-100 dark:bg-gray-700 flex">
        @foreach ($tabs as $tab)
            <button 
                role="tab" 
                aria-controls="{{ $tab }}-tab" 
                :tabindex="tab === '{{ $tab }}' ? 0 : -1" 
                :aria-selected="tab === '{{ $tab }}' ? 'true' : 'false'" 
                class="px-5 py-3 text-current text-sm leading-tight -mb-px opacity-50 hover:opacity-100 transition duration-200 ease-in-out" 
                :class="{ 'bg-white dark:bg-gray-800 opacity-100': tab === '{{ $tab }}' }" 
                @click.prevent="tab = '{{ $tab }}'"
            >
                {{ Filament::formatLabel($tab) }}
            </button>
        @endforeach
    </div>
    <div class="p-5 rounded-b shadow bg-white dark:bg-gray-800">
        {{ $slot }}
    </div>
</div>