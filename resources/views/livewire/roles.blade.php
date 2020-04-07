@section('title', __('filament::admin.roles'))

<div>
    @foreach ($roles as $role)
        <div>{{ $role->name }}</div>
    @endforeach
    {{ $roles->links('filament::partials.links') }}
</div>