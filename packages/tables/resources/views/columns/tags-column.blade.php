@php
    $state = $getTags();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class(['px-4 py-3 flex flex-wrap gap-1']) }}>
    @foreach ($getTags() as $tag)
        <span class="inline-flex items-center justify-center h-6 px-2 text-sm font-medium tracking-tight rounded-full text-primary-700 bg-primary-500/10">
            {{ $tag }}
        </span>
    @endforeach
</div>
