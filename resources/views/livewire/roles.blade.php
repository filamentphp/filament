<div>
    @foreach ($roles as $role)
        <div>{{ $role->name }}</div>
    @endforeach
    {{ $roles->links('filament::partials.links') }}
</div>