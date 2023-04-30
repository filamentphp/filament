<div class="filament-resource-relation-manager">
    {{ \Filament\Facades\Filament::renderHook('resource.relation-manager.start') }}

    {{ $this->table }}

    {{ \Filament\Facades\Filament::renderHook('resource.relation-manager.end') }}
</div>
