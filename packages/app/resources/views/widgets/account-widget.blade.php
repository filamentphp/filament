<x-filament-widgets::widget class="filament-account-widget">
    <x-filament-widgets::card>
        @php
            $user = \Filament\Facades\Filament::auth()->user();
        @endphp

        <div class="h-12 flex items-center space-x-4 rtl:space-x-reverse">
            <x-filament::user-avatar :user="$user" />

            <div>
                <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                    {{ __('filament::widgets/account-widget.welcome', ['user' => \Filament\Facades\Filament::getUserName($user)]) }}
                </h2>

                <form action="{{ \Filament\Facades\Filament::getLogoutUrl() }}" method="post" class="text-sm">
                    @csrf

                    <button
                        type="submit"
                        class="text-gray-600 hover:text-primary-500 focus:outline-none focus:underline dark:text-gray-300 dark:hover:text-primary-500"
                    >
                        {{ __('filament::widgets/account-widget.buttons.logout.label') }}
                    </button>
                </form>
            </div>
        </div>
    </x-filament-widgets::card>
</x-filament-widgets::widget>
