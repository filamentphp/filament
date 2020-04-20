@section('title', $title)

@section('actions')
    <button 
        type="button" 
        @click.prevent="$dispatch('filament-toggle-modal', { id: 'user-delete' })" 
        class="btn btn-small btn-danger"
    >
        <x-heroicon-o-trash class="h-3 w-3 mr-2" />
        {{ __('filament::users.delete') }}
    </button>
    @push('footer')
        <x-filament-modal 
            id="user-delete" 
            :title="__('filament::users.delete')" 
            :esc-close="true" 
            :click-outside="true"
            class="sm:max-w-md"
        >
            @livewire('filament::user-delete', ['user' => $user])
        </x-filament-modal>
    @endpush
@endsection

<div class="grid grid-cols-1 md:grid-cols-7 gap-4 lg:gap-8">

    <div class="md:col-span-5">
        @livewire('filament::user-edit', ['user' => $user])
    </div>

    <x-filament-well class="md:col-span-2">

        <dl class="grid grid-cols-2 gap-2 md:gap-3 text-xs leading-tight">

            <dt>{{ __('filament::labels.created_at') }}</dt>
            <dd class="text-right">{{ $user->created_at->fromNow() }}</dd>
        
            <dt>{{ __('filament::labels.updated_at') }}</dt>
            <dd class="text-right">{{ $user->updated_at->fromNow() }}</dd>
        
            <dt>{{ __('Last login') }}</dt>
            <dd class="text-right">{{ $user->last_login_at ? $user->last_login_at->fromNow() : __('never') }}</dd>
        
            @if ($user->last_login_ip)
                <dt>{{ __('Last login IP') }}</dt>
                <dd class="text-right">{{ $user->last_login_ip }}</dd>
            @endif 
        
        </dl>

    </x-filament-well>

</div>
