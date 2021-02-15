@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        <span>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default leading-5 rounded">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button wire:click="previousPage" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded hover:text-gray-500 focus:outline-none focus:shadow-outline-secondary focus:border-secondary-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </button>
            @endif
        </span>

        <span>
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded hover:text-gray-500 focus:outline-none focus:shadow-outline-secondary focus:border-secondary-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default leading-5 rounded">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </span>
    </nav>
@endif
