@props([
    'paginator',
    'pageOptions' => [],
])

@php
    $isRtl = __('filament::layout.direction') === 'rtl';
    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
@endphp

<nav
    aria-label="{{ __('filament-tables::table.pagination.label') }}"
    role="navigation"
    {{ $attributes->class(['fi-ta-pagination grid grid-cols-3 items-center']) }}
>
    @if (! $paginator->onFirstPage())
        <x-filament::button
            color="gray"
            rel="prev"
            :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
            @class([
                'justify-self-start',
                'inline-grid md:hidden' => ! $isSimple,
            ])
        >
            {{ __('filament-tables::table.pagination.actions.previous.label') }}
        </x-filament::button>
    @endif

    @if (! $isSimple)
        <span
            class="hidden text-sm font-medium text-gray-700 dark:text-gray-200 md:inline"
        >
            {{
                trans_choice(
                    'filament-tables::table.pagination.overview',
                    $paginator->total(),
                    [
                        'first' => \Filament\Support\format_number($paginator->firstItem() ?? 0),
                        'last' => \Filament\Support\format_number($paginator->lastItem() ?? 0),
                        'total' => \Filament\Support\format_number($paginator->total()),
                    ],
                )
            }}
        </span>
    @endif

    @if (count($pageOptions) > 1)
        <div class="col-start-2 justify-self-center">
            <label class="sm:hidden">
                <x-filament-forms::affixes>
                    <x-filament::input.select
                        wire:model.live="tableRecordsPerPage"
                    >
                        @foreach ($pageOptions as $option)
                            <option value="{{ $option }}">
                                {{ $option === 'all' ? __('filament-tables::table.pagination.fields.records_per_page.options.all') : $option }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament-forms::affixes>

                <span class="sr-only">
                    __('filament-tables::table.pagination.fields.records_per_page.label')
                </span>
            </label>

            <label class="hidden sm:inline">
                <x-filament-forms::affixes
                    :prefix="__('filament-tables::table.pagination.fields.records_per_page.label')"
                >
                    <x-filament::input.select
                        wire:model.live="tableRecordsPerPage"
                    >
                        @foreach ($pageOptions as $option)
                            <option value="{{ $option }}">
                                {{ $option === 'all' ? __('filament-tables::table.pagination.fields.records_per_page.options.all') : $option }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament-forms::affixes>
            </label>
        </div>
    @endif

    @if ($paginator->hasMorePages())
        <x-filament::button
            color="gray"
            rel="next"
            :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
            @class([
                'col-start-3 justify-self-end',
                'inline-grid md:hidden' => ! $isSimple,
            ])
        >
            {{ __('filament-tables::table.pagination.actions.next.label') }}
        </x-filament::button>
    @endif

    @if (! $isSimple)
        <ol
            class="hidden justify-self-end rounded-lg bg-white shadow-sm ring-1 ring-gray-950/10 dark:bg-white/5 dark:ring-white/20 md:flex"
        >
            @if (! $paginator->onFirstPage())
                <x-filament-tables::pagination.item
                    :aria-label="__('filament-tables::table.pagination.actions.previous.label')"
                    :icon="$isRtl ? 'heroicon-m-chevron-right' : 'heroicon-m-chevron-left'"
                    icon-alias="tables::pagination.previous-button"
                    rel="prev"
                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                />
            @endif

            @foreach ($paginator->render()->offsetGet('elements') as $element)
                @if (is_string($element))
                    <x-filament-tables::pagination.item
                        disabled
                        :label="$element"
                    />
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <x-filament-tables::pagination.item
                            :active="$page === $paginator->currentPage()"
                            :aria-label="trans_choice('filament-tables::table.pagination.actions.go_to_page.label', $page, ['page' => $page])"
                            :label="$page"
                            :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                            :wire:key="$this->getId() . '.table.pagination.' . $paginator->getPageName() . '.' . $page"
                        />
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <x-filament-tables::pagination.item
                    :aria-label="__('filament-tables::table.pagination.actions.next.label')"
                    :icon="$isRtl ? 'heroicon-m-chevron-left' : 'heroicon-m-chevron-right'"
                    icon-alias="tables::pagination.next-button"
                    rel="next"
                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                />
            @endif
        </ol>
    @endif
</nav>
