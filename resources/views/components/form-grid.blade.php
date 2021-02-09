@props([
    'columns' => 1,
])

<div class="grid grid-cols-1 lg:grid-cols-{{ $columns }} gap-6">
    {{ $slot }}
</div>
