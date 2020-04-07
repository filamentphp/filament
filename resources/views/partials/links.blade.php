@if ($paginator->hasPages())
    <ul class="relative z-0 inline-flex shadow-sm rounded" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="relative inline-flex items-center px-2 py-2 rounded-l border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-400" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <x-heroicon-s-cheveron-left class="h-5 w-5" />
            </li>
        @else
            <li>
                <button type="button" class="relative inline-flex items-center px-2 py-2 rounded-l border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" wire:click="previousPage" rel="prev" aria-label="@lang('pagination.previous')">
                    <x-heroicon-s-cheveron-left class="h-5 w-5" />
                </button>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-700" aria-disabled="true">
                    {{ $element }}
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="hidden md:inline-flex -ml-px relative items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-400 dark:text-gray-300" aria-current="page">
                            {{ $page }}
                        </li>
                    @else
                        <li>
                            <button type="button" class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-700 dark:text-gray-400 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" wire:click="gotoPage({{ $page }})">
                                {{ $page }}
                            </button>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <button type="button" class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" wire:click="nextPage" rel="next" aria-label="@lang('pagination.next')">
                    <x-heroicon-s-cheveron-right class="h-5 w-5" />
                </button>
            </li>
        @else
            <li class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm leading-5 font-medium text-gray-400" aria-disabled="true" aria-label="@lang('pagination.next')">
                <x-heroicon-s-cheveron-right class="h-5 w-5" />
            </li>
        @endif
    </ul>
@endif
