<div class="block px-4 py-2 text-xs text-gray-400">
    {{ __('Manage Users') }}
</div>

@can('create', Laravel\Jetstream\Jetstream::newUserModel())
    <x-jet-dropdown-link href="/users/create">
        {{ __('Create New User') }}
    </x-jet-dropdown-link>
@endcan

<div class="border-t border-gray-100"></div>