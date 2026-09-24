<x-filament-panels::page>
    <span id="top-pagination-label" class="fi-sr-only">Top pagination</span>

    <span id="bottom-pagination-label" class="fi-sr-only">
        Bottom pagination
    </span>

    <x-filament::pagination
        aria-labelledby="top-pagination-label"
        data-testid="top-pagination"
        :paginator="$paginator"
        extreme-links
        wire-key-prefix="top-pagination"
    />

    <x-filament::pagination
        aria-labelledby="bottom-pagination-label"
        data-testid="bottom-pagination"
        :paginator="$paginator"
        extreme-links
    />
</x-filament-panels::page>
