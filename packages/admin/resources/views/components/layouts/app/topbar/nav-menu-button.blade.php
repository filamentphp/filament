<button
    x-data="{}"
    x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
    @class([
        'filament-sidebar-open-button shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none',
        'lg:-ml-[44px] lg:w-6 lg:h-6 lg:text-gray-500 lg:bg-gray-200 lg:dark:bg-gray-600 lg:dark:text-gray-400 lg:z-10 lg:hover:bg-white lg:dark:hover:bg-black lg:focus:bg-gray-300 lg:dark:focus:bg-gray-400 lg:focus:text-white',
        'lg:hidden' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
        'lg:mr-4 rtl:lg:ml-4 rtl:lg:-mr-[44px]' => config('filament.layout.sidebar.is_collapsible_on_desktop'),
    ])
>
    <div class="relative lg:absolute visible lg:invisible">
        <x-heroicon-o-menu class=" w-6 h-6 transition" />
    </div>
    <div class="absolute ltr:lg:relative invisible ltr:lg:visible">
        <x-heroicon-o-chevron-left class="w-4 h-4 transition" x-show="$store.sidebar.isOpen" />
        <x-heroicon-o-chevron-right class="w-4 h-4 transition" x-show="!$store.sidebar.isOpen" />
    </div>
    <div class="absolute rtl:lg:relative invisible rtl:lg:visible">
        <x-heroicon-o-chevron-left class="w-4 h-4 transition" x-show="!$store.sidebar.isOpen" />
        <x-heroicon-o-chevron-right class="w-4 h-4 transition" x-show="$store.sidebar.isOpen" />
    </div>
</button>
