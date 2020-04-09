@section('title', $title)

<div>
    <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($users as $user)
            <li wire:key="{{ $user->id }}" class="flex">
                <a class="flex-grow p-4 bg-white dark:bg-gray-800 shadow rounded flex items-center" href="{{ route('filament.admin.users.edit', ['id' => $user->id]) }}">
                    <div class="flex-shrink-0 flex">
                        @livewire('filament::user-avatar', [
                            'userId' => $user->id, 
                            'size' => 96, 
                            'classes' => 'h-12 w-12 rounded-full'
                        ], key($user->id))
                    </div>
                    <div class="ml-3 flex-grow flex justify-between">
                        <div class="flex-grow">
                            <div class="text-lg font-medium">{{ $user->name }}</div>
                            <div class="text-xs leading-none font-mono text-gray-400">
                                {{ $user->email }}
                            </div>
                        </div>                        
                        <div class="flex-shrink-0">
                            @if ($user->is_super_admin)
                                <x-filament-pill>{{ __('filament::permissions.super_admin') }}</x-filament-pill>
                            @elseif (count($user->roles))
                                <ul>
                                    @foreach($user->roles as $role)
                                        <li><x-filament-pill>{{ $role->name }}</x-filament-pill></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>              
                    </div>            
                </a>  
            </li>
        @endforeach
    </ul>

    {{ $users->links('filament::partials.links') }}
</div>