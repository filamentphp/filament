<div {{ $attributes->class([
    'px-6 py-4 filament-global-search-no-results-message',
    'dark:text-gray-200' => config('filament.dark_mode'),
]) }}>
    {{ __('filament::global-search.no_results_message') }}
</div>
