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
    ][$formComponent->getColumnSpan()]
@endphp

<section
    x-data="{ open: {{ $formComponent->isCollapsed() ? 'false' : 'true' }} }"
    aria-labelledby="{{ $formComponent->getId() }}-heading"
    class="space-y-4 p-4 rounded border border-gray-200 bg-gray-50 {{ $columnSpanClass }}"
>
    <div class="flex items-start justify-between space-x-4">
        <div class="space-y-1">
            @if ($heading = $formComponent->getHeading())
                <h2 id="{{ $formComponent->getId() }}-heading" class="text-lg font-medium leading-tight">
                    {{ __($heading) }}
                </h2>
            @endif

            @if ($subheading = $formComponent->getSubheading())
                <p class="text-sm text-gray-500">
                    {{ __($subheading) }}
                </p>
            @endif
        </div>

        <div class="flex">
            @if ($formComponent->isCollapsible())
                <button
                    aria-controls="{{ $formComponent->getId() }}-content"
                    x-on:click.prevent="open = ! open"
                    class="flex p-2 -m-2 text-gray-400 transition-colors duration-200 hover:text-gray-700"
                >
                    <x-heroicon-o-chevron-down class="w-4 h-4" x-show="!open" />

                    <x-heroicon-o-chevron-up class="w-4 h-4" x-show="open" />

                    <span class="sr-only">{{ __('Toggle section content') }}</span>
                </button>
            @endif
        </div>
    </div>

    <div id="{{ $formComponent->getId() }}-content" x-show.transition="open" :aria-expanded="open.toString()">
        <x-forms::layout :schema="$formComponent->getSchema()" :columns="$formComponent->getColumns()" />
    </div>
</section>
