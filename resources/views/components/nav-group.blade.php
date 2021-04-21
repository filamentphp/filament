@props([
    'activeRule',
    'label',
    'icon',
    'items',
])

<li x-data="{ subNavOpen : {{ (request()->is($activeRule)) ? 'true' : 'false' }} }">
    <a
        @click="subNavOpen = !subNavOpen"
        {{ $attributes->merge(['class' => 'px-4 py-3 flex items-center space-x-3 rtl:space-x-reverse rounded transition-color duration-200 hover:text-white ' . (request()->is($activeRule) ? 'text-white bg-gray-700' : 'text-current')]) }}
    >
        <x-dynamic-component :component="$icon" class="flex-shrink-0 w-5 h-5" />

        <span class="flex-grow text-sm font-medium leading-tight">{{ __($label) }}</span>
    </a>
    <ol :class="{ 'block' : subNavOpen , 'hidden' : !subNavOpen}"  @click.away="subNavOpen = false">
        @foreach($items as $item)
            <li class="border-l-2 {{ (request()->is($item->activeRule)) ? 'border-gray-700' : 'border-gray-300' }}">
                <x-filament::nav-link
                    :active-rule="$item->activeRule"
                    :icon="$item->icon"
                    :label="$item->label"
                    :url="$item->url"
                />
            </li>
        @endforeach
    </ol>
</li>
