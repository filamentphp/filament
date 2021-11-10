<div>
    @if ($this->isEnabled())
        <label for="globalSearchQueryInput" class="sr-only">
            Global search
        </label>

        <div class="relative group max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 text-gray-500 transition pointer-events-none group-focus-within:text-primary-500">
                <x-heroicon-o-search class="w-5 h-5" />
            </span>

            <input
                wire:model.debounce.500ms="searchQuery"
                id="globalSearchQueryInput"
                placeholder="Search"
                type="search"
                class="block w-full h-10 pl-10 lg:text-lg bg-gray-200 placeholder-gray-500 border-gray-200 transition duration-75 rounded-lg focus:bg-white focus:placeholder-gray-400 focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600"
            >

            @if ($results !== null)
                <div
                    x-data="{ isOpen: true }"
                    x-show="isOpen"
                    x-on:keydown.escape.window="isOpen = false"
                    x-on:click.away="isOpen = false"
                    x-on:open-global-search-results.window="isOpen = true"
                    class="absolute right-0 top-auto z-10 mt-2 shadow-xl overflow-hidden rounded-xl w-screen max-w-xs sm:max-w-lg"
                >
                    <div class="overflow-y-scroll overflow-x-hidden max-h-96 bg-white shadow rounded-xl">
                        @forelse ($results as $type => $typeResults)
                            <ul class="divide-y">
                                <li class="sticky top-0 z-10">
                                    <header class="px-6 py-2 bg-gray-50/80 backdrop-blur-xl backdrop-saturate-150">
                                        <p class="text-xs font-bold tracking-wider text-gray-500 uppercase">
                                            {{ $type }}
                                        </p>
                                    </header>
                                </li>

                                @foreach ($typeResults as $result)
                                    <li>
                                        <a href="{{ $result['url'] }}" class="relative block px-6 py-4 transition focus:bg-gray-500/5 hover:bg-gray-500/5 focus:ring-1 focus:ring-gray-300">
                                            <p class="font-medium">{{ $result['title'] }}</p>

                                            <p class="text-sm space-x-2 font-medium text-gray-500">
                                                @foreach ($result['details'] as $label => $value)
                                                    <span>
                                                        <span class="font-medium text-gray-700">{{ $label }}:</span> <span>{{ $value }}</span>
                                                    </span>
                                                @endforeach
                                            </p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @empty
                            <div class="px-6 py-4">
                                No search results found.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
