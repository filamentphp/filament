@props([
    'paginator',
    'recordsPerPageSelectOptions',
])

@php
    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $isRtl = __('filament::layout.direction') === 'rtl';
    $previousArrowIcon = $isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left';
    $nextArrowIcon = $isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right';
@endphp

<nav
    role="navigation"
    aria-label="{{ __('tables::table.pagination.label') }}"
    class="flex items-center justify-between filament-tables-pagination"
>
    <div class="flex justify-between items-center flex-1 lg:hidden">
        <div class="w-10">
            @if ($paginator->hasPages() && (! $paginator->onFirstPage()))
                <x-tables::icon-button
                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                    rel="prev"
                    :icon="$previousArrowIcon"
                    :label="__('tables::table.pagination.buttons.previous.label')"
                />
            @endif
        </div>

        <x-tables::pagination.records-per-page-selector :options="$recordsPerPageSelectOptions" />

        <div class="w-10">
            @if ($paginator->hasPages() && $paginator->hasMorePages())
                <x-tables::icon-button
                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                    rel="next"
                    :icon="$nextArrowIcon"
                    :label="__('tables::table.pagination.buttons.next.label')"
                />
            @endif
        </div>
    </div>

    <div class="hidden flex-1 items-center lg:grid grid-cols-3">
        <div class="flex items-center">
            @if ($isSimple)
                @if (! $paginator->onFirstPage())
                    <x-tables::button
                        :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                        :icon="$previousArrowIcon"
                        rel="prev"
                        size="sm"
                        color="secondary"
                    >
                        {{ __('tables::table.pagination.buttons.previous.label') }}
                    </x-tables::button>
                @endif
            @else
                <div @class([
                    'pl-2 text-sm font-medium',
                    'dark:text-white' => config('tables.dark_mode'),
                ])>
                    @if ($paginator->total() > 1)
                        {{ __('tables::table.pagination.overview', [
                            'first' => $paginator->firstItem(),
                            'last' => $paginator->lastItem(),
                            'total' => $paginator->total(),
                        ]) }}
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center">
            <x-tables::pagination.records-per-page-selector :options="$recordsPerPageSelectOptions" />
        </div>

        <div class="flex items-center justify-end">
            @if ($isSimple)
                @if ($paginator->hasMorePages())
                    <x-tables::button
                        :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                        :icon="$nextArrowIcon"
                        icon-position="after"
                        rel="next"
                        size="sm"
                        color="secondary"
                    >
                        {{ __('tables::table.pagination.buttons.next.label') }}
                    </x-tables::button>
                @endif
            @else
                @if ($paginator->hasPages())
                    <div @class([
                        'py-3 border rounded-lg',
                        'dark:border-gray-600' => config('tables.dark_mode'),
                    ])>
                        <ol @class([
                            'flex items-center text-sm text-gray-500 divide-x divide-gray-300',
                            'dark:text-gray-400' => config('tables.dark_mode'),
                        ])>
                            @if (! $paginator->onFirstPage())
                                <x-tables::pagination.item
                                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                                    icon="heroicon-s-chevron-left"
                                    aria-label="{{ __('tables::table.pagination.buttons.previous.label') }}"
                                    rel="prev"
                                />
                            @endif

                            @foreach ($paginator->render()->offsetGet('elements') as $element)
                                @if (is_string($element))
                                    <x-tables::pagination.item :label="$element" disabled />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        <x-tables::pagination.item
                                            :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                                            :label="$page"
                                            :aria-label="__('tables::table.pagination.buttons.go_to_page.label', ['page' => $page])"
                                            :active="$page === $paginator->currentPage()"
                                            :wire:key="'pagination-' . $paginator->getPageName() . '-page' . $page"
                                        />
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($paginator->hasMorePages())
                                <x-tables::pagination.item
                                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                                    icon="heroicon-s-chevron-right"
                                    aria-label="{{ __('tables::table.pagination.buttons.next.label') }}"
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
