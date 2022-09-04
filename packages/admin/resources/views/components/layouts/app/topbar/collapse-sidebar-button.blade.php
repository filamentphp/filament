<button
    x-data="{}"
    x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
    @class([
        'filament-sidebar-open-button shrink-0 flex items-center justify-center w-10 h-10 transition text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none lg:z-10 lg:hover:bg-white lg:focus:bg-white lg:hover:text-gray-600 lg:focus:text-gray-600 lg:-ml-[44px] lg:w-6 lg:h-6 lg:text-gray-500 lg:bg-white lg:shadow',
        'lg:dark:bg-gray-900 lg:dark:hover:text-gray-400 lg:dark:focus:text-gray-400' => config('filament.dark_mode'),
        'lg:hidden' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
        'lg:mr-4 rtl:lg:ml-4 rtl:lg:-mr-[44px]' => config('filament.layout.sidebar.is_collapsible_on_desktop'),
    ])
>
    <div class="relative lg:absolute visible lg:invisible">
        <x-heroicon-o-menu class="w-6 h-6" />
    </div>

    <div class="absolute ltr:lg:relative invisible ltr:lg:visible">
        <x-heroicon-s-chevron-left class="w-4 h-4" x-show="$store.sidebar.isOpen" />
        <x-heroicon-s-chevron-right class="w-4 h-4" x-show="! $store.sidebar.isOpen" />
    </div>

    <div class="absolute rtl:lg:relative invisible rtl:lg:visible">
        <x-heroicon-s-chevron-left class="w-4 h-4" x-show="! $store.sidebar.isOpen" />
        <x-heroicon-s-chevron-right class="w-4 h-4" x-show="$store.sidebar.isOpen" />
    </div>
</button>
