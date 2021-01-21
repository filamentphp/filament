<div class="p-4 md:p-6 bg-white shadow-sm rounded overflow-hidden">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg h-64 flex items-center justify-center">
        <a href="{{ route('filament.resource', ['action' => $createAction, 'resource' => (new $resource)->getSlug()]) }}" class="text-sm font-mono">Create</a>
    </div>
</div>
