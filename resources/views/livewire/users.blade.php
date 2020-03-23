<div>
    <ul class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($users as $user)
            <li wire:key="{{ $user->id }}" class="flex">
                <a class="flex-grow p-4 bg-white shadow rounded flex items-center" href="{{ route('filament.admin.users.edit', ['user' => $user->id]) }}">
                    @livewire('filament::user-avatar', [
                        'userId' => $user->id, 
                        'size' => 96, 
                        'classes' => 'h-12 w-12 rounded-full'
                    ], key($user->id))
                    <div class="ml-3 flex-grow flex justify-between">
                        <div>
                            <div class="text-lg font-medium">{{ $user->name }}</div>
                            <div class="text-xs leading-none font-mono text-gray-400">
                                {{ $user->email }}
                            </div>
                        </div>
                        @if ($user->is_super_admin)
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-gray-100 text-gray-800">
                                    {{ __('filament::permissions.super_admin') }}
                                </span>
                            </div>
                        @endif
                    </div>            
                </a>  
            </li>
        @endforeach
    </ul>

    {{ $users->links('filament::partials.links') }}
</div>