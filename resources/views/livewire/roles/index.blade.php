@section('title', $title)

@section('actions')
    @can('create roles')
        <button 
            type="button" 
            @click.prevent="$dispatch('filament-toggle-modal', { id: 'role-create' })" 
            class="btn btn-small btn-add"
        >
            <x-heroicon-o-plus class="h-3 w-3 mr-2" />
            {{ __('filament::roles.create') }}
        </button>
        @push('footer')
            <x-filament-modal 
                id="role-create" 
                :label="__('filament::roles.create')" 
                :esc-close="true" 
                :click-outside="true"
                class="sm:max-w-xl"
            >
                @livewire('filament::role-create')
            </x-filament-modal>
        @endpush
    @endcan
@endsection

<div>

    <table class="table-simple">
        <thead>                    
            <tr>
                <th>
                    <button class="flex" wire:click.prevent="sortBy('id')">
                        @include('filament::partials.sort-header', [
                            'field' => 'id',
                            'label' => __('ID'),
                        ])
                    </button>
                </th>
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
                <th>
                    <button class="flex" wire:click.prevent="sortBy('created_at')">
                        @include('filament::partials.sort-header', [
                            'field' => 'created_at',
                            'label' => __('filament::labels.created_at'),
                        ])
                    </button>
                </th>
                <th colspan="2">
                    <button class="flex" wire:click.prevent="sortBy('updated_at')">
                        @include('filament::partials.sort-header', [
                            'field' => 'updated_at',
                            'label' => __('filament::labels.updated_at'),
                        ])
                    </button>    
                </th>
            </tr> 
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td class="font-medium">{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>{{ $role->created_at->fromNow() }}</td>
                    <td>{{ $role->updated_at->fromNow() }}</td>
                    <td class="text-right">
                        <x-filament-dropdown dropdown-class="origin-top-right right-0 w-48">
                            <x-slot name="button">
                                <x-heroicon-o-dots-horizontal class="h-5 w-5" />
                            </x-slot>
                            @can('edit roles')
                                <button @click.prevent="open = false; $dispatch('filament-toggle-modal', { id: 'role-edit-{{ $role->id }}' })" type="button">{{ __('Edit') }}</button>
                                @push('footer')
                                    <x-filament-modal 
                                        :id="'role-edit-'.$role->id" 
                                        :label="__('filament::roles.edit')" 
                                        :esc-close="true" 
                                        :click-outside="true" 
                                        class="sm:max-w-xl"
                                    >
                                        @livewire('filament::role-edit', ['role' => $role])
                                    </x-filament-modal> 
                                @endpush
                            @endcan
                            @can('delete roles')
                                <button @click.prevent="open = false; $dispatch('filament-toggle-modal', { id: 'role-delete-{{ $role->id }}' })" type="button" class="text-red-500" type="button">{{ __('Delete') }}</button>
                                @push('footer')      
                                    <x-filament-modal 
                                        :id="'role-delete-'.$role->id" 
                                        :label="__('filament::roles.delete')" 
                                        :esc-close="true" 
                                        :click-outside="true" 
                                        class="sm:max-w-md"
                                    >
                                        @livewire('filament::role-delete', ['role' => $role])
                                    </x-filament-modal>
                                @endpush
                            @endcan
                        </x-filament-dropdown>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="4">{{ __('No roles found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">
        {{ $roles->links('filament::partials.pagination') }}
    </div>

</div>
