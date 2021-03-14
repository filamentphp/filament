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

<section x-data="{ open: true }" aria-labelledby="section-heading-{{ $formComponent->getId() }}" class="{{ $columnSpanClass }} space-y-4 p-4 rounded border border-gray-200 bg-gray-50">
    <div class="flex items-start justify-between space-x-4">
        <div class="space-y-1">
        @if ($formComponent->getHeading())
            <h2 id="section-heading-{{ $formComponent->getId() }}" class="text-lg font-medium leading-tight">
                {{ __($formComponent->getHeading()) }}
            </h2>
        @endif

        @if ($formComponent->getSubheading())
            <p class="text-sm text-gray-500">
                {{ __($formComponent->getSubheading()) }}
            </p>
        @endif
        </div>
        
        <div class="flex">
            <button aria-controls="section-content-{{ $formComponent->getId() }}" @click.prevent="open = !open" class="flex p-2 -m-2 text-gray-400 transition-colors duration-200 hover:text-gray-700">
                <x-heroicon-o-chevron-down class="w-4 h-4" x-show="!open" />
                <x-heroicon-o-chevron-up class="w-4 h-4" x-show="open" />
                <span class="sr-only">{{ __('Toggle section content') }}</span>
            </button>
        </div>
    </div>
    <div id="section-content-{{ $formComponent->getId() }}" x-show.transition="open" :aria-expanded="open.toString()">
        <x-forms::layout :schema="$formComponent->getSchema()" :columns="$formComponent->getColumns()" />
    </div>
</section>
