<footer class="border-t px-6 py-3 flex shrink-0 items-center gap-3 filament-sidebar-footer">
    @php
        $user = \Filament\Facades\Filament::auth()->user();
    @endphp

    <div
        class="w-11 h-11 rounded-full bg-gray-200 bg-cover bg-center"
        style="background-image: url('{{ \Filament\Facades\Filament::getUserAvatarUrl($user) }}')"
    ></div>

    <div>
        <p class="text-sm font-bold">
            {{ \Filament\Facades\Filament::getUserName($user) }}
        </p>

        <p class="text-xs text-gray-500 hover:text-gray-700 focus:text-gray-700">
            <a href="{{ route('filament.auth.logout') }}">
                {{ __('filament::layout.buttons.logout.label') }}
            </a>
        </p>
    </div>
</footer>
