<div>
    <ul class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ($users as $user)
            <li wire:key="{{ $user->id }}" class="flex">
                <a class="flex-grow p-4 bg-white shadow-sm rounded-md flex items-center" href="{{ route('filament.admin.users.edit', ['user' => $user->id]) }}">
                    @livewire('filament::user-avatar', [
                        'user' => $user, 
                        'size' => 96, 
                        'classes' => 'h-12 w-12 rounded-full'
                    ], key($user->id))
                    <div class="ml-3">
                        <div class="text-lg font-medium">{{ $user->name }}</div>
                        <div class="text-xs leading-none font-mono text-gray-400">
                            {{ $user->email }}
                        </div>
                    </div>            
                </a>  
            </li>
        @endforeach
    </ul>

    {{ $users->links('filament::partials.links') }}
</div>