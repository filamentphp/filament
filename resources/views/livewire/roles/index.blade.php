@section('title', $title)

@section('actions')
    <button 
        type="button" 
        @click.prevent="$dispatch('filament-toggle-modal', { id: 'role-create' })" 
        class="btn btn-small btn-add"
    >
        <x-heroicon-o-plus class="h-3 w-3 mr-2" />
        {{ __('filament::permissions.roles.create') }}
    </button>
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
                <th colspan="2">
                    <button class="flex" wire:click.prevent="sortBy('description')">
                        @include('filament::partials.sort-header', [
                            'field' => 'description',
                            'label' => __('Description'),
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
                    <td class="text-right">
                        <x-filament-dropdown dropdown-class="origin-top-right right-0 w-48">
                            <x-slot name="button">
                                <x-heroicon-o-dots-horizontal class="h-5 w-5" />
                            </x-slot>
                            <button @click.prevent="open = false; $dispatch('filament-toggle-modal', { id: 'edit-role-{{ $role->id }}' })" type="button">{{ __('Edit') }}</button>
                            <button @click.prevent="open = false; $dispatch('filament-toggle-modal', { id: 'delete-role-{{ $role->id }}' })" type="button" class="text-red-500" type="button">{{ __('Delete') }}</button>
                        </x-filament-dropdown>
                    </td>
                </tr>
                @push('footer')
                    <x-filament-modal 
                        :id="'edit-role-'.$role->id" 
                        :label="__('filament::permissions.roles.edit')" 
                        :esc-close="true" 
                        :click-outside="true" 
                        class="sm:max-w-xl"
                    >
                        @livewire('filament::role-edit', ['role' => $role])
                    </x-filament-modal>       
                    <x-filament-modal 
                        :id="'delete-role-'.$role->id" 
                        :label="__('filament::permissions.roles.delete')" 
                        :esc-close="true" 
                        :click-outside="true" 
                        class="sm:max-w-md"
                    >
                        @livewire('filament::role-delete', ['role' => $role])
                    </x-filament-modal>
                @endpush
            @empty
                <tr>
                    <td class="text-center" colspan="4">{{ __('No roles found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">
        {{ $roles->links('filament::partials.links') }}
    </div>

</div>

@push('footer')
    <x-filament-modal 
        id="role-create" 
        :label="__('filament::permissions.roles.create')" 
        :esc-close="true" 
        :click-outside="true"
        class="sm:max-w-xl"
    >
        @livewire('filament::role-create')
    </x-filament-modal>
@endpush
