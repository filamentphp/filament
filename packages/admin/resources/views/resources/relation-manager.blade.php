<div class="filament-resource-relation-manager">
    {{ \Filament\Facades\Filament::renderHook('relation-manager.start') }}
    
    {{ $this->table }}

    {{ \Filament\Facades\Filament::renderHook('relation-manager.end') }}
</div>
