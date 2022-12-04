<x-filament-widgets::widget class="filament-account-widget">
    <x-filament::card>
        @php
            $user = filament()->auth()->user();
        @endphp

        <div class="h-12 flex items-center space-x-4 rtl:space-x-reverse">
            <x-filament::avatar.user :user="$user" />

            <div>
                <h2 class="text-xl font-medium tracking-tight">
                    {{ __('filament::widgets/account-widget.welcome', ['user' => filament()->getUserName($user)]) }}
                </h2>

                <form action="{{ filament()->getLogoutUrl() }}" method="post" class="text-sm">
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
    </x-filament::card>
</x-filament-widgets::widget>
