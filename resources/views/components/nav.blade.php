<nav aria-label="primary" class="flex-grow px-4">
    <ol>
    @foreach(config('filament.nav', []) as $nav)
        <li>
            <x-filament::nav-link 
                :path="$nav['path']" 
                :active="$nav['active']" 
                :label="$nav['label']" 
                :icon="$nav['icon'] ?? false" />
        </li>
    @endforeach
    </ol>
</nav>