<dl class="grid grid-cols-2 gap-2 md:gap-3 text-xs leading-tight">

    <dt class="text-gray-400">{{ __('filament::admin.created_at') }}</dt>
    <dd class="text-right">{{ $user->created_at->fromNow() }}</dd>

    <dt class="text-gray-400">{{ __('filament::admin.updated_at') }}</dt>
    <dd class="text-right">{{ $user->updated_at->fromNow() }}</dd>

    <dt class="text-gray-400">{{ __('filament::user.last_login_at') }}</dt>
    <dd class="text-right">{{ $user->last_login_at ? $user->last_login_at->fromNow() : __('filament::user.last_login_never') }}</dd>

    @if ($user->last_login_ip)
        <dt class="text-gray-400">{{ __('filament::user.last_login_ip') }}</dt>
        <dd class="text-right">{{ $user->last_login_ip }}</dd>
    @endif 

</dl>