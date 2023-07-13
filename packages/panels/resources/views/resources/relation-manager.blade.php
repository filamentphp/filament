<div class="fi-resource-relation-manager">
    {{ filament()->renderHook('resource.relation-manager.start') }}

    {{ $this->table }}

    {{ filament()->renderHook('resource.relation-manager.end') }}
</div>
