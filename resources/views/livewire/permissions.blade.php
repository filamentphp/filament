@section('title', $title)

<div>

    <div class="mb-4 flex justify-between">
        <div class="flex-grow mr-6">
            <input wire:model="search" type="search" class="form-input input w-full" placeholder="{{ __('Search...') }}">
        </div>
        <label class="flex-shrink-0 flex items-center">
            <span class="label mr-2">Per Page:</span>
            <select wire:model="perPage" class="form-select select">
                <option>{{ $this->perPage }}</option>
                <option>15</option>
                <option>25</option>
            </select>
        </label>
    </div>

    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="table-simple">
                <table>
                    <thead>                    
                        <tr>
                            <th>
                                <button class="flex" wire:click.prevent="sortBy('name')">
                                    @include('filament::partials.sort-header', [
                                        'field' => 'name',
                                        'label' => __('Name'),
                                    ])
                                </button>
                            </th>
                            <th>
                                <button class="flex" wire:click.prevent="sortBy('description')">
                                    @include('filament::partials.sort-header', [
                                        'field' => 'description',
                                        'label' => __('Description'),
                                    ])
                                </button>
                            </th>
                            <th colspan="2">
                                <button class="flex" wire:click.prevent="sortBy('is_system')">
                                    @include('filament::partials.sort-header', [
                                        'field' => 'is_system',
                                        'label' => __('Type'),
                                    ])
                                </button>
                            </th>
                        </tr> 
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="font-medium">{{ $item->name }}</td>
                                <td>{{ $item->description }}</td>
                                <td>       
                                    <x-filament-pill>
                                        @if ($item->is_system)
                                            {{ __('system') }}
                                        @else
                                            {{ __('custom') }}
                                        @endif
                                    </x-filament-pill>
                                </td>
                                <td class="font-medium text-right">
                                    {{ $item->action }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="3">{{ __('No permissions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex justify-between mt-6">
        {{ $results->links('filament::partials.links') }}
        @if (count($results))
            <p class="text-xs font-mono leading-5 text-gray-500 dark:text-gray-400">
                {{ __('filament::admin.pagination_results', [
                    'firstItem' => $results->firstItem(),
                    'lastItem' => $results->lastItem(),
                    'total' => $results->total(),
                ]) }}
            </p>
        @endif
    </div>

</div>