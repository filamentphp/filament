<div x-data="{ tab: '{{ $tab }}' }" {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow']) }}>
    <div role="tablist" class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex">
        @foreach ($tabs as $value => $label)
            <button 
                role="tab" 
                aria-controls="{{ $value }}-tab" 
                :tabindex="tab === '{{ $value }}' ? 0 : -1" 
                :aria-selected="tab === '{{ $value }}' ? 'true' : 'false'" 
                class="border-r border-gray-200 dark:border-gray-700 px-5 py-3 text-gray-500 text-sm leading-tight -mb-px transition duration-200 ease-in-out" 
                :class="{ 'text-current bg-white dark:bg-gray-700': tab === '{{ $value }}' }" 
                @click.prevent="tab = '{{ $value }}'"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</div>