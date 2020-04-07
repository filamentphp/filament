@section('title', __('filament::admin.roles'))

<div>

    <x-filament-table :headers="$headers" :rows="$rows" />
    
    {{ $roles->links('filament::partials.links') }}

</div>