<nav aria-label="primary" {{ $attributes }}>
    <ol class="space-y-1">
        @foreach($nav as $item)
            <li>
                <x-filament::nav-link 
                    :path="$item['path']" 
                    :active="$item['active']" 
                    :label="$item['label']" 
                    :icon="$item['icon']" />
            </li>
        @endforeach
    </ol>
</nav>