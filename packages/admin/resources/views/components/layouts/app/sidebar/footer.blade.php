<footer class="border-t px-6 py-3 flex shrink-0 items-center gap-3">
    @php
        $user = \Filament\Facades\Filament::auth()->user();
    @endphp

    <div
        class="w-11 h-11 rounded-full bg-gray-200 bg-cover bg-center"
        style="background-image: url('{{ \Filament\Facades\Filament::getUserAvatarUrl($user) }}')"
    ></div>

    <a href="{{ route('filament.auth.logout') }}">
        <span class="text-sm font-bold">
            {{ \Filament\Facades\Filament::getUserName($user) }}
        </span>
        <span class="text-xs text-gray-500 hover:text-gray-700 focus:text-gray-700">
            {{ __('filament::layout.buttons.logout.label') }}
        </span>
    </a>
</footer>
