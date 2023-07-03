@props([
    'paginator',
    'recordsPerPageSelectOptions' => [],
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
    {{ $attributes->class(['filament-tables-pagination flex items-center justify-between']) }}
>
    <div class="flex flex-1 items-center justify-between lg:hidden">
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

        @if (count($recordsPerPageSelectOptions) > 1)
            <x-tables::pagination.records-per-page-selector
                :options="$recordsPerPageSelectOptions"
            />
        @endif

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

    <div class="hidden flex-1 grid-cols-3 items-center lg:grid">
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
                <div
                    @class([
                        'pl-2 text-sm font-medium',
                        'dark:text-white' => config('tables.dark_mode'),
                    ])
                >
                    {{
                        trans_choice(
                            'tables::table.pagination.overview',
                            $paginator->total(),
                            [
                                'first' => $paginator->firstItem(),
                                'last' => $paginator->lastItem(),
                                'total' => $paginator->total(),
                            ],
                        )
                    }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center">
            @if (count($recordsPerPageSelectOptions) > 1)
                <x-tables::pagination.records-per-page-selector
                    :options="$recordsPerPageSelectOptions"
                />
            @endif
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
                    <div
                        @class([
                            'rounded-lg border py-3',
                            'dark:border-gray-600' => config('tables.dark_mode'),
                        ])
                    >
                        <ol
                            @class([
                                'flex items-center gap-px divide-x divide-gray-300 text-sm text-gray-500 rtl:divide-x-reverse',
                                'dark:divide-gray-600 dark:text-gray-400' => config('tables.dark_mode'),
                            ])
                        >
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
                                    <x-tables::pagination.item
                                        :label="$element"
                                        disabled
                                    />
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        <x-tables::pagination.item
                                            :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                                            :label="$page"
                                            :aria-label="trans_choice('tables::table.pagination.buttons.go_to_page.label', $page, ['page' => $page])"
                                            :active="$page === $paginator->currentPage()"
                                            :wire:key="$this->id . '.table.pagination.' . $paginator->getPageName() . '.' . $page"
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
