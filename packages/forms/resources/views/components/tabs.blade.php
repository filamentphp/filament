@php
    $columnSpanClass = [
        '',
        'lg:col-span-1',
        'lg:col-span-2',
        'lg:col-span-3',
        'lg:col-span-4',
        'lg:col-span-5',
        'lg:col-span-6',
        'lg:col-span-7',
        'lg:col-span-8',
        'lg:col-span-9',
        'lg:col-span-10',
        'lg:col-span-11',
        'lg:col-span-12',
    ][$formComponent->columnSpan]
@endphp

<div
    x-data="{ tab: '{{ count($formComponent->getTabsConfig()) ? array_key_first($formComponent->getTabsConfig()) : null }}', tabs: {{ json_encode($formComponent->getTabsConfig()) }} }"
    x-on:switch-tab.window="if ($event.detail in tabs) tab = $event.detail"
    x-cloak
    {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
    class="{{ $columnSpanClass }} bg-white border border-gray-200 rounded p-4 md:p-6"
>
    <div class="-m-4 md:-m-6">
        <div {!! __($formComponent->label) ? 'aria-label="' . __($formComponent->label) . '"' : null !!} role="tablist"
             class="flex overflow-hidden bg-gray-100 rounded-t">
            @foreach ($formComponent->getTabsConfig() as $tabId => $tabLabel)
                <button type="button"
                        aria-controls="{{ $tabId }}-tab"
                        x-bind:aria-selected="tab === '{{ $tabId }}'"
                        x-on:click="tab = '{{ $tabId }}'"
                        role="tab"
                        x-bind:tabindex="tab === '{{ $tabId }}' ? 0 : -1"
                        class="p-3 text-sm font-medium leading-tight border-r border-gray-200 md:px-6"
                        x-bind:class="{ 'bg-white': tab === '{{ $tabId }}' }"
                >
                    {{ __($tabLabel) }}
                </button>
            @endforeach
        </div>

        @foreach ($formComponent->schema as $tab)
            {{ $tab->render() }}
        @endforeach
    </div>
</div>
