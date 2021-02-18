<nav aria-label="primary" {{ $attributes }}>
    <ol class="space-y-1">
        @foreach ($items as $item)
            <li>
                <x-filament::nav-link
                    :active-rule="$item->activeRule"
                    :icon="$item->icon"
                    :label="__($item->label)"
                    :url="$item->url"
                />
            </li>
        @endforeach
    </ol>
</nav>
