<div class="fi-resource-relation-manager">
    {{ \Filament\Support\Facades\FilamentView::renderHook('resource.relation-manager.start', scope: static::class) }}

    {{ $this->table }}

    {{ \Filament\Support\Facades\FilamentView::renderHook('resource.relation-manager.end', scope: static::class) }}
</div>
