@props([
    'table',
])

<div class="flex shadow-sm">
    @if ($table->isFilterable())
        <select wire:model="filter" class="text-sm flex-shrink-0 sm:text-base focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-gray-300 {{ $table->isSearchable() ? 'rounded-l' : 'rounded' }}">
            <option>{{ __('tables::table.filter.placeholder') }}</option>

            @foreach ($table->getVisibleFilters() as $filter)
                <option value="{{ $filter->getName() }}">{{ __($filter->getLabel()) }}</option>
            @endforeach
        </select>
    @endif

    @if ($table->isSearchable())
        <div class="relative flex-grow">
            <input
                type="search"
                wire:model="search"
                placeholder="{{ __('tables::table.search.placeholder') }}"
                class="text-sm sm:text-base pl-10 block w-full placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-gray-300 {{ $table->isFilterable() ? 'rounded-r border-l-0' : 'rounded' }}"
            />

            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" aria-hidden="true">
                <x-heroicon-o-search class="w-5 h-5" wire:loading.remove wire:target="search" />

                <svg wire:loading wire:target="search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor" class="w-5 h-5 transition-all duration-300">
                    <path d="M6.306 28.014c1.72 10.174 11.362 17.027 21.536 15.307C38.016 41.6 44.87 31.958 43.15 21.784l-4.011.678c1.345 7.958-4.015 15.502-11.974 16.847-7.959 1.346-15.501-4.014-16.847-11.973l-4.011.678z">
                    <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur=".7s" repeatCount="indefinite"/></path>
                </svg>
            </div>
        </div>
    @endif
</div>
