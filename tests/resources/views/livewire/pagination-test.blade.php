<div>
    <x-filament::pagination
        data-testid="top-pagination"
        :paginator="$paginator"
        extreme-links
        wire-key-prefix="top-pagination"
    />

    <x-filament::pagination
        data-testid="bottom-pagination"
        :paginator="$paginator"
        extreme-links
    />
</div>
