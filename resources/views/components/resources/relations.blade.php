@props([
    'owner',
    'relations' => [],
])

<div class="space-y-6">
    @foreach ($relations as $manager)
        @livewire(\Livewire\Livewire::getAlias($manager, $manager::getName()), ['owner' => $owner], key($loop->index))
    @endforeach
</div>
