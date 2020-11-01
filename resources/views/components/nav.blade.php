<nav aria-label="primary" class="flex-grow px-4">
    <ol>
        <li>
            <x-filament::nav-link 
                :path="route('filament.dashboard')" 
                active="filament.dashboard" 
                :label="__('Dashboard')" 
                heroicon="o-home" />
        </li>
    </ol>
</nav>