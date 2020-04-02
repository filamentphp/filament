<div x-data="darkMode()">
    <span :class="{ 'bg-gray-200': !isDark, 'bg-indigo-600': isDark }" class="relative inline-block flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:shadow-outline" role="checkbox" tabindex="0" @click="toggle()" @keydown.space.prevent="toggle()" :aria-checked="isDark.toString()">
        <span aria-hidden="true" :class="{ 'translate-x-5': isDark, 'translate-x-0': !isDark }" class="relative inline-block h-5 w-5 rounded-full bg-white shadow transform transition ease-in-out duration-200">
            <span :class="{ 'opacity-0 ease-out duration-100': isDark, 'opacity-100 ease-in duration-200': !isDark }" class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity">
                <x-heroicon-s-sun class="h-3 w-3 text-gray-400" />
            </span>
            <span :class="{ 'opacity-100 ease-in duration-200': isDark, 'opacity-0 ease-out duration-100': !isDark }" class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity">
                <x-heroicon-s-moon class="h-3 w-3 text-indigo-600" />
            </span>
        </span>
    </span>
</div>

<script>
    function darkMode() {
        return {
            isDark: checkDarkMode(),
            toggle() { 
                this.isDark = !this.isDark 
                document.documentElement.classList.toggle('mode-dark')
                setCookie('filament_color_scheme', this.isDark ? 'dark' : 'light')
            },
        }
    }
</script>