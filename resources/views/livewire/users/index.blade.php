@section('title', $title)

@section('actions')
    @can('create users')
        <button 
            type="button" 
            @click.prevent="$dispatch('filament-toggle-modal', { id: 'user-create' })" 
            class="btn btn-small btn-add"
        >
            <x-heroicon-o-plus class="h-3 w-3 mr-2" />
            {{ __('filament::users.create') }}
        </button>
        @push('footer')
            <x-filament-modal 
                id="user-create" 
                :label="__('filament::permissions.create')" 
                :esc-close="true" 
                :click-outside="true"
                class="sm:max-w-3xl"
            >
                @livewire('filament::user-create')
            </x-filament-modal>
        @endpush
    @endcan
@endsection

<div>
    <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($users as $user)
            <li wire:key="{{ $user->id }}" class="flex flex-grow p-4 bg-white dark:bg-gray-800 shadow rounded flex items-center">
                <a href="{{ route('filament.admin.users.edit', ['id' => $user->id]) }}" class="flex-shrink-0 flex">
                    @livewire('filament::user-avatar', [
                        'userId' => $user->id, 
                        'size' => 96, 
                        'classes' => 'h-12 w-12 rounded-full'
                    ], key($user->id))
                </a>
                <div class="ml-3 flex-grow flex justify-between">
                    <div class="flex-grow">
                        <h2 class="text-lg leading-tight font-medium">
                            <a href="{{ route('filament.admin.users.edit', ['id' => $user->id]) }}">{{ $user->name }}</a>
                        </h2>
                        <a 
                            href="mailto:{{ $user->email }}" 
                            class="inline-block text-xs leading-none font-mono text-gray-400 hover:text-indigo-500"
                        >
                            {{ $user->email }}
                        </a>
                    </div>                        
                    <div class="flex-shrink-0">
                        <x-filament-dropdown dropdown-class="origin-top-right right-0 w-48">
                            <x-slot name="button">
                                <x-heroicon-o-dots-horizontal class="h-5 w-5" />
                            </x-slot>
                            <a href="{{ route('filament.admin.users.edit', ['id' => $user->id]) }}">{{ __('filament::users.edit') }}</a>
                            @can('delete users')
                                <button 
                                    type="button" 
                                    @click.prevent="$dispatch('filament-toggle-modal', { id: 'user-delete-{{ $user->id }}' })" 
                                    class="text-red-500"
                                >
                                    {{ __('filament::users.delete') }}
                                </button>
                                @push('footer')
                                    <x-filament-modal 
                                        :id="'user-delete-'.$user->id" 
                                        :label="__('filament::users.delete')" 
                                        :esc-close="true" 
                                        :click-outside="true"
                                        class="sm:max-w-md"
                                    >
                                        @livewire('filament::user-delete', ['user' => $user])
                                    </x-filament-modal>
                                @endpush
                            @endcan
                        </x-filament-dropdown>
                    </div>              
                </div>            
            </li>
        @endforeach
    </ul>

    {{ $users->links('filament::partials.pagination') }}
</div>
