@php
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\SupportIconAlias;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Number;

    $isRtl = __('filament-panels::layout.direction') === 'rtl';
    $isSimple = ! $paginator instanceof LengthAwarePaginator;
@endphp

@if ((! $isSimple) && $paginator->hasPages())
    <ol class="fi-pagination-items">
        @if (! $paginator->onFirstPage())
            @if ($extremeLinks)
                <x-filament::pagination.item
                    :aria-label="__('filament::components/pagination.actions.first.label')"
                    :icon="$isRtl ? Heroicon::ChevronDoubleRight : Heroicon::ChevronDoubleLeft"
                    :icon-alias="
                        $isRtl
                        ? SupportIconAlias::PAGINATION_FIRST_BUTTON_RTL
                        : SupportIconAlias::PAGINATION_FIRST_BUTTON
                    "
                    rel="first"
                    :wire:click="'gotoPage(1, \'' . $paginator->getPageName() . '\')'"
                    :wire:key="$this->getId() . '.pagination.first'"
                />
            @endif

            <x-filament::pagination.item
                :aria-label="__('filament::components/pagination.actions.previous.label')"
                :icon="$isRtl ? Heroicon::ChevronRight : Heroicon::ChevronLeft"
                {{-- @deprecated Use `SupportIconAlias::PAGINATION_PREVIOUS_BUTTON_RTL` instead of `SupportIconAlias::PAGINATION_PREVIOUS_BUTTON` for RTL. --}}
                :icon-alias="
                    $isRtl
                    ? [
                        SupportIconAlias::PAGINATION_PREVIOUS_BUTTON_RTL,
                        SupportIconAlias::PAGINATION_PREVIOUS_BUTTON,
                    ]
                    : SupportIconAlias::PAGINATION_PREVIOUS_BUTTON
                "
                rel="prev"
                :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                :wire:key="$this->getId() . '.pagination.previous'"
            />
        @endif

        @foreach ($paginator->render()->offsetGet('elements') as $element)
            @if (is_string($element))
                <x-filament::pagination.item disabled :label="$element" />
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <x-filament::pagination.item
                        :active="$page === $paginator->currentPage()"
                        :aria-label="trans_choice('filament::components/pagination.actions.go_to_page.label', $page, ['page' => Number::format($page)])"
                        :label="Number::format($page)"
                        :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                        :wire:key="$this->getId() . '.pagination.' . $paginator->getPageName() . '.' . $page"
                    />
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <x-filament::pagination.item
                :aria-label="__('filament::components/pagination.actions.next.label')"
                :icon="$isRtl ? Heroicon::ChevronLeft : Heroicon::ChevronRight"
                {{-- @deprecated Use `SupportIconAlias::PAGINATION_NEXT_BUTTON_RTL` instead of `SupportIconAlias::PAGINATION_NEXT_BUTTON` for RTL. --}}
                :icon-alias="
                    $isRtl
                    ? [
                        SupportIconAlias::PAGINATION_NEXT_BUTTON_RTL,
                        SupportIconAlias::PAGINATION_NEXT_BUTTON,
                    ]
                    : SupportIconAlias::PAGINATION_NEXT_BUTTON
                "
                rel="next"
                :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                :wire:key="$this->getId() . '.pagination.next'"
            />

            @if ($extremeLinks)
                <x-filament::pagination.item
                    :aria-label="__('filament::components/pagination.actions.last.label')"
                    :icon="$isRtl ? Heroicon::ChevronDoubleLeft : Heroicon::ChevronDoubleRight"
                    :icon-alias="
                        $isRtl
                        ? SupportIconAlias::PAGINATION_LAST_BUTTON_RTL
                        : SupportIconAlias::PAGINATION_LAST_BUTTON
                    "
                    rel="last"
                    :wire:click="'gotoPage(' . $paginator->lastPage() . ', \'' . $paginator->getPageName() . '\')'"
                    :wire:key="$this->getId() . '.pagination.last'"
                />
            @endif
        @endif
    </ol>
@endif
