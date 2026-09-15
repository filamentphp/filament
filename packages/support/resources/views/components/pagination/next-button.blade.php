@php
    use Illuminate\Contracts\Pagination\CursorPaginator;
@endphp

@if ($paginator->hasMorePages())
    @php
        if ($paginator instanceof CursorPaginator) {
            $wireClickAction = "setPage('{$paginator->nextCursor()->encode()}', '{$paginator->getCursorName()}')";
        } else {
            $wireClickAction = "nextPage('{$paginator->getPageName()}')";
        }
    @endphp

    <x-filament::button
        color="gray"
        rel="next"
        :wire:click="$wireClickAction"
        :wire:key="$this->getId() . '.pagination.next'"
        class="fi-pagination-next-btn"
    >
        {{ __('filament::components/pagination.actions.next.label') }}
    </x-filament::button>
@endif
