<div class="fi-resource-relation-manager">
    {{ \Filament\Support\Facades\FilamentView::renderHook('resource.relation-manager.start') }}

    {{ $this->table }}

    {{ \Filament\Support\Facades\FilamentView::renderHook('resource.relation-manager.end') }}
</div>
