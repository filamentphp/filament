<x-filament::card class="flex">
    <div class="flex items-center space-x-4 rtl:space-x-reverse">
        @php
            $user = \Filament\Facades\Filament::auth()->user();
        @endphp

        <div
            class="w-11 h-11 rounded-full bg-gray-200 bg-cover bg-center"
            style="background-image: url('{{ \Filament\Facades\Filament::getAvatarUrl($user) }}')"
        ></div>

        <div class="space-y-1">
            <h2 class="text-2xl">Welcome, {{ $user->getFilamentName() }}</h2>

            <p class="text-sm">
                <a href="{{ route('filament.auth.logout') }}" class="link">
                    Sign out
                </a>
            </p>
        </div>
    </div>
</x-filament::card>
