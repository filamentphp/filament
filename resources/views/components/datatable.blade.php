@props([
    'resource',
    'model',
    'headings' => [],
    'sortField',
    'sortDirection',
])

<div class="space-y-4">
    <div class="flex items-center justify-between space-x-4">
        <div class="relative flex-grow max-w-screen-md">
            <x-forms::text-input
                type="search"
                model="search"
                :placeholder="__('filament::datatable.search', ['resource' => Str::plural($resource)])"
                class="pl-10"
                autocomplete="off"
            />
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" aria-hidden="true">
                <x-heroicon-o-search class="w-5 h-5" wire:loading.remove />
                <x-filament::loader class="w-5 h-5" wire:loading />
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <label class="text-sm leading-tight font-medium cursor-pointer">
                {{ __('filament::datatable.perPage') }}
            </label>
            <x-filament::select model="perPage" id="per-page">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </x-filament::select>
        </div>
    </div>

    <x-filament::table>
        <x-slot name="head">
            @foreach($headings as $key => $heading)
                @empty ($heading['sortable'])
                    <x-filament::table.heading>
                        {{ $heading['label'] ?? $key }}
                    </x-filament::table.heading>
                @else
                    <x-filament::table.heading
                        sortable
                        wire:click="sortBy('{{ $key }}')"
                        :direction="$sortField === $key ? $sortDirection : null"
                    >
                        {{ $heading['label'] ?? $key }}
                    </x-filament::table.heading>
                @endif
            @endforeach
        </x-slot>
        <x-slot name="body">
            @forelse ($model as $item)
                <x-filament::table.row wire:loading.class.delay="opacity-50">
                    @foreach($headings as $key => $heading)
                        <x-filament::table.cell>
                            @empty ($heading['link'])
                                {{ $item->$key }}
                            @else
                                <a
                                    href="{{ route('filament.resource', [
                                        'resource' => $resource,
                                        'action' => 'edit',
                                        'id' => $item->{$heading['link']},
                                    ]) }}"
                                    class="link"
                                >
                                    {{ $item->$key }}
                                </a>
                            @endif
                        </x-filament::table.cell>
                    @endforeach
                </x-filament::table.row>
            @empty
                <x-filament::table.row>
                    <x-filament::table.cell :colspan="count($headings)">
                        <div class="flex items-center justify-center h-16">
                            <p class="text-gray-500 font-mono text-xs">{{ __('filament::datatable.noresults', ['resource' => Str::plural($resource)]) }}</p>
                        </div>
                    </x-filament::table.cell>
                </x-filament::table.row>
            @endforelse
        </x-slot>
    </x-filament::table>

    {{ $model->links() }}
</div>
