<ul class="px-6 grid grid-cols-4 lg:grid-cols-8 xl:grid-cols-12 gap-4">
@foreach ($users as $user)
    <li class="col-span-4 flex">
        <a href="#" class="flex-grow bg-white rounded shadow p-2 md:p-4 flex space-x-4 overflow-hidden">
            <img src="{{ $user->avatar(48) }}" srcset="{{ $user->avatar(48) }} 1x, {{ $user->avatar(96) }} 2x" alt="{{ $user->name }}" class="flex-shrink-0 w-12 h-12 rounded-full shadow-md" />
            <div class="flex-grow">
                <h3 class="leading-tight">{{ $user->name }}</h3>
                <span class="font-mono text-sm leading-tight text-gray-600">{{ $user->email }}</span>
            </div>
        </a>
    </li>
@endforeach
</ul>

{{--
<x-slot name="actions">
    Actions
</x-slot>
--}}