@section('title', $title)

<div>

    <div class="mb-4 flex justify-between">
        <div class="flex-grow mr-6">
            <input wire:model="search" type="search" class="form-input input w-full" placeholder="{{ __('Search...') }}">
        </div>
        <label class="flex-shrink-0 flex items-center">
            <span class="label mr-2">Per Page:</span>
            <select wire:model="perPage" class="form-select input">
                @foreach($this->pagingOptions as $option)
                    <option>{{ $option }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <table class="table-simple">
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
            @forelse ($permissions as $permission)
                <tr>
                    <td class="font-medium">{{ $permission->name }}</td>
                    <td>{{ $permission->description }}</td>
                    <td {!! $permission->is_system ? 'colspan="2"' : '' !!}>       
                        <x-filament-pill>
                            @if ($permission->is_system)
                                {{ __('system') }}
                            @else
                                {{ __('custom') }}
                            @endif
                        </x-filament-pill>
                    </td>
                    @if (!$permission->is_system)
                        <td class="text-right">
                            <x-filament-dropdown dropdown-class="origin-top-right right-0 w-48">
                                <x-slot name="button">
                                    <x-heroicon-o-dots-horizontal class="h-5 w-5" />
                                </x-slot>
                                <a href="{{ route('filament.admin.permissions.edit', ['id' => $permission->id]) }}">{{ __('Edit') }}</a>
                                <button type="button" class="text-red-500">{{ __('Delete') }}</button>
                            </x-filament-dropdown>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="3">{{ __('No permissions found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex justify-between mt-6">
        {{ $permissions->links('filament::partials.links') }}
        @if (count($permissions))
            <p class="text-xs font-mono leading-5 text-gray-500 dark:text-gray-400">
                {{ __('filament::admin.pagination_results', [
                    'firstItem' => $permissions->firstItem(),
                    'lastItem' => $permissions->lastItem(),
                    'total' => $permissions->total(),
                ]) }}
            </p>
        @endif
    </div>

</div>