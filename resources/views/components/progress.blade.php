@props([
    'max' => 100,
    'progress',
])
<div {{ $attributes }}>
    <progress max="{{ $max }}" x-bind:value="{{ $progress }}" class="sr-only"></progress>
    <div class="rounded-full border border-gray-200 overflow-hidden" aria-hidden="true">
        <div class="h-1 bg-gray-500" :style="`width: ${ {{ ((int) $progress / (int) $max) * 100 }} }%`"></div>
    </div>  
</div>
