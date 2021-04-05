@props([
    'owner',
    'subResources' => [],
])

<div class="space-y-6">
    @foreach ($subResources as $subResource)
        @livewire(\Livewire\Livewire::getAlias($subResource, $subResource::getName()), ['owner' => $owner], key($loop->index))
    @endforeach
</div>
