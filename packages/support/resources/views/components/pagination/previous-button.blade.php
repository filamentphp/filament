@php
    use Illuminate\Contracts\Pagination\CursorPaginator;
@endphp

@if (! $paginator->onFirstPage())
    @php
        if ($paginator instanceof CursorPaginator) {
            $wireClickAction = "setPage('{$paginator->previousCursor()->encode()}', '{$paginator->getCursorName()}')";
        } else {
            $wireClickAction = "previousPage('{$paginator->getPageName()}')";
        }
    @endphp

    <x-filament::button
        color="gray"
        rel="prev"
        :wire:click="$wireClickAction"
        :wire:key="$this->getId() . '.pagination.previous'"
        class="fi-pagination-previous-btn"
    >
        {{ __('filament::components/pagination.actions.previous.label') }}
    </x-filament::button>
@endif
