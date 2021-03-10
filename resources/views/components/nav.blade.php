<nav aria-label="primary" {{ $attributes }}>
    <ol>
        @foreach ($items as $item)
            <li>
                <x-filament::nav-link
                    :active-rule="$item->activeRule"
                    :icon="$item->icon"
                    :label="$item->label"
                    :url="$item->url"
                />
            </li>
        @endforeach
    </ol>
</nav>
