@section('title', $title)

@section('actions')
    <button class="btn btn-small btn-add">
        <x-heroicon-o-plus class="h-4 w-4 mr-2" />
        {{ __('Add Role') }}
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
                            <a href="{{ route('filament.admin.roles.edit', ['id' => $role->id]) }}">{{ __('Edit') }}</a>
                            <button type="button" class="text-red-500">{{ __('Delete') }}</button>
                        </x-filament-dropdown>
                    </td>
                </tr>
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