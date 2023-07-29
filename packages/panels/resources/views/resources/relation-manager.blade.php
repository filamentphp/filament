<div class="fi-resource-relation-manager">
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.relation-manager.before', scopes: $this->getRenderHookScopes()) }}

    {{ $this->table }}

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.relation-manager.after', scopes: $this->getRenderHookScopes()) }}
</div>
