<nav aria-label="primary" {{ $attributes }}>
    <ol class="space-y-1">
        @foreach ($items as $item)
            <li>
                <x-filament::nav-link
                    :url="$item->url"
                    :active="$item->active"
                    :label="__($item->label)"
                    :icon="$item->icon"
                />
            </li>
        @endforeach
    </ol>
</nav>
