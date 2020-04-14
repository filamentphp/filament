@section('title', $title)

@section('actions')
    <button @click.prevent="$dispatch('filament-toggle-modal', { id: 'new-role' })" class="btn btn-small btn-add">
        <x-heroicon-o-plus class="h-3 w-3 mr-2" />
        {{ __('New Role') }}
    </button>
@endsection

<div>

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
                    <td class="font-medium">{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    <td class="text-right">
                        <x-filament-dropdown dropdown-class="origin-top-right right-0 w-48">
                            <x-slot name="button">
                                <x-heroicon-o-dots-horizontal class="h-5 w-5" />
                            </x-slot>
                            <button @click.prevent="open = false; $dispatch('filament-toggle-modal', { id: '{{ $role->id }}' })" type="button">{{ __('Edit') }}</button>
                            <button type="button" class="text-red-500" type="button">{{ __('Delete') }}</button>
                        </x-filament-dropdown>
                    </td>
                </tr>
                @push('footer')
                    <x-filament-modal :id="$role->id" :label="__('filament::permissions.role.edit')">
                        @livewire('filament::role-edit', ['id' => $role->id])
                    </x-filament-modal>
                @endpush
            @empty
                <tr>
                    <td class="text-center" colspan="3">{{ __('No roles found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">
        {{ $roles->links('filament::partials.links') }}
    </div>

</div>

@push('footer')
    <x-filament-modal id="new-role" :label="__('New Role')" :esc-close="true" :click-outside="true">
        <h2>{{ __('New Role') }}</h2>
    </x-filament-modal>
@endpush
