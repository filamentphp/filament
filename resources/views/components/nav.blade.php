<nav aria-label="primary" {{ $attributes }}>
    <ol>
        @foreach ($items as $item)

        @if ($item instanceof \Filament\View\NavigationGroup)
            <x-filament::nav-group
                :active-rule="$item->activeRule()"
                :icon="$item->icon"
                :label="$item->label"
                :items="$item->items"
            />
        @else
                <li>
                    <x-filament::nav-link
                        :active-rule="$item->activeRule"
                        :icon="$item->icon"
                        :label="$item->label"
                        :url="$item->url"
                    />
                </li>
        @endif
        @endforeach
    </ol>
</nav>
