@props([
    'isResourceGroup',
    'activeRule',
    'parentActiveRule',
    'label',
    'icon',
    'items',
    'url',
])

@php
$subNavLabel = 'subNavOpen' . Str::studly($label);
@endphp
<li x-data="{ {{ $subNavLabel }} : {{ (request()->is($activeRule)) ? 'true' : 'false' }} }">
    <div class="inline-flex w-full">
    <a
        @if($isResourceGroup)
        href="{{ $url }}"
        @else
        @click="{{ $subNavLabel }} = !{{ $subNavLabel }}"
        @endif
        {{ $attributes->merge(['class' => 'px-4 py-3 flex flex-grow items-center space-x-3 rtl:space-x-reverse rounded transition-color duration-200 hover:text-white ' . (request()->is($activeRule) ? 'text-white' : 'text-current')]) }}
    >
        <x-dynamic-component :component="$icon" class="flex-shrink-0 w-5 h-5" />

        <span class="flex-grow text-sm font-medium leading-tight">{{ __($label) }}</span>
    </a>
    <button @click="{{ $subNavLabel }} = !{{ $subNavLabel }}" class="px-3">
        <x-heroicon-o-chevron-down class="w-4" x-show="true === {{ $subNavLabel }}" />
        <x-heroicon-o-chevron-right class="w-4" x-show="false === {{ $subNavLabel }}" />
    </button>

    </div>
    <ol :class="{ 'block' : {{ $subNavLabel }} , 'hidden' : !{{ $subNavLabel }}}"  @click.away="{{ $subNavLabel }} = false">
        @foreach($items as $item)
            <li>
                <x-filament::nav-link
                    class="pl-8"
                    :active-rule="$item->activeRule"
                    :icon="$item->icon"
                    :label="$item->label"
                    :url="$item->url"
                />
            </li>
        @endforeach
    </ol>
</li>
