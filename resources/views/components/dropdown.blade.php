<div @click.away="open = false" x-data="{ open: false }" {{ $attributes->merge(['class' => 'dropdown']) }}>
    <button type="button" @click.prevent="open = !open" class="dropdown-button {{ $buttonClass }}">
        {{ $button }}
    </button>
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95" 
        class="dropdown-content {{ $dropdownClass }}"
    >
        {{ $slot }}
    </div>
</div>