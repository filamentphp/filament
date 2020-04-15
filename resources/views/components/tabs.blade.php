<div x-data="{ tab: '{{ $tab }}' }" {{ $attributes->merge(['class' => 'shadow rounded overflow-hidden bg-white dark:bg-gray-800']) }}>
    <div role="tablist" class="bg-gray-100 dark:bg-gray-700 flex">
        @foreach ($tabs as $value => $label)
            <button 
                role="tab" 
                aria-controls="{{ $value }}-tab" 
                :tabindex="tab === '{{ $value }}' ? 0 : -1" 
                :aria-selected="tab === '{{ $value }}' ? 'true' : 'false'" 
                class="px-5 py-3 text-gray-500 hover:text-current text-sm leading-tight -mb-px transition duration-200 ease-in-out" 
                :class="{ 'text-current bg-white dark:bg-gray-800': tab === '{{ $value }}' }" 
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