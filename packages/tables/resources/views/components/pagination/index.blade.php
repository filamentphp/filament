@props([
    'paginator',
    'pageOptions',
])

@php
    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $isRtl = __('filament::layout.direction') === 'rtl';
    $previousArrowIcon = $isRtl ? 'heroicon-m-chevron-right' : 'heroicon-m-chevron-left';
    $nextArrowIcon = $isRtl ? 'heroicon-m-chevron-left' : 'heroicon-m-chevron-right';
@endphp

<nav
    role="navigation"
    aria-label="{{ __('filament-tables::table.pagination.label') }}"
    class="filament-tables-pagination flex items-center justify-between"
>
    <div class="flex justify-between items-center flex-1 lg:hidden">
        <div class="w-10">
            @if ($paginator->hasPages() && (! $paginator->onFirstPage()))
                <x-filament::icon-button
                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                    rel="prev"
                    :icon="$previousArrowIcon"
                    icon-alias="tables::pagination.buttons.previous"
                    :label="__('filament-tables::table.pagination.buttons.previous.label')"
                />
            @endif
        </div>

        @if (count($pageOptions) > 1)
            <x-filament-tables::pagination.records-per-page-selector :options="$pageOptions" />
        @endif

        <div class="w-10">
            @if ($paginator->hasPages() && $paginator->hasMorePages())
                <x-filament::icon-button
                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                    rel="next"
                    :icon="$nextArrowIcon"
                    icon-alias="tables::pagination.buttons.next"
                    :label="__('filament-tables::table.pagination.buttons.next.label')"
                />
            @endif
        </div>
    </div>

    <div class="hidden flex-1 items-center lg:grid grid-cols-3">
        <div class="flex items-center">
            @if ($isSimple)
                @if (! $paginator->onFirstPage())
                    <x-filament::button
                        :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                        :icon="$previousArrowIcon"
                        rel="prev"
                        size="sm"
                        color="gray"
                    >
                        {{ __('filament-tables::table.pagination.buttons.previous.label') }}
                    </x-filament::button>
                @endif
            @else
                <div class="pl-2 text-sm font-medium dark:text-white">
                    {{ trans_choice(
                        'filament-tables::table.pagination.overview',
                        $paginator->total(),
                        [
                            'first' => \Filament\Support\format_number($paginator->firstItem()),
                            'last' => \Filament\Support\format_number($paginator->lastItem()),
                            'total' => \Filament\Support\format_number($paginator->total()),
                        ],
                    ) }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center">
            @if (count($pageOptions) > 1)
                <x-filament-tables::pagination.records-per-page-selector :options="$pageOptions" />
            @endif
        </div>

        <div class="flex items-center justify-end">
            @if ($isSimple)
                @if ($paginator->hasMorePages())
                    <x-filament::button
                        :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                        :icon="$nextArrowIcon"
                        icon-position="after"
                        rel="next"
                        size="sm"
                        color="gray"
                    >
                        {{ __('filament-tables::table.pagination.buttons.next.label') }}
                    </x-filament::button>
                @endif
            @else
                @if ($paginator->hasPages())
                    <div class="py-3 border rounded-lg dark:border-gray-600">
                        <ol class="flex items-center text-sm text-gray-500 divide-x rtl:divide-x-reverse divide-gray-300 dark:text-gray-400 dark:divide-gray-600">
                            @if (! $paginator->onFirstPage())
                                <x-filament-tables::pagination.item
                                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                                    icon="heroicon-m-chevron-left"
                                    aria-label="{{ __('filament-tables::table.pagination.buttons.previous.label') }}"
                                    rel="prev"
                                />
                            @endif

                            @foreach ($paginator->render()->offsetGet('elements') as $element)
                                @if (is_string($element))
                                    <x-filament-tables::pagination.item :label="$element" disabled />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        <x-filament-tables::pagination.item
                                            :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                                            :label="$page"
                                            :aria-label="__('filament-tables::table.pagination.buttons.go_to_page.label', ['page' => $page])"
                                            :active="$page === $paginator->currentPage()"
                                            :wire:key="$this->id . '.table.pagination.' . $paginator->getPageName() . '.' . $page"
                                        />
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($paginator->hasMorePages())
                                <x-filament-tables::pagination.item
                                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                                    icon="heroicon-m-chevron-right"
                                    aria-label="{{ __('filament-tables::table.pagination.buttons.next.label') }}"
                                    rel="next"
                                />
                            @endif
                        </ol>
                    </div>
                @endif
            @endif
        </div>
    </div>
</nav>
