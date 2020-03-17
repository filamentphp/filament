<div>
    <ul class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ($users as $user)
            <li wire:key="{{ $user->id }}" class="flex">
                <a class="flex-grow p-4 bg-white shadow-sm rounded-md flex items-center" href="{{ route('alpine.admin.users.edit', ['user' => $user->id]) }}">
                    <img src="{{ $user->avatar(96) }}" 
                        class="h-12 w-12 rounded-full"
                        alt="{{ $user->name }}" 
                        width="48" 
                        height="48"
                        loading="lazy">
                    <div class="ml-3">
                        <strong class="text-lg font-medium">{{ $user->name }}</strong>
                        <div class="text-xs font-mono text-gray-400">
                            {{ $user->email }}
                        </div>
                    </div>            
                </a>  
            </li>
        @endforeach
    </ul>

    {{ $users->links('alpine::partials.links') }}
</div>