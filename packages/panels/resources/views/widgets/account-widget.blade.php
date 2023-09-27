@php
    $user = filament()->auth()->user();
@endphp

<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            @if (filament()->auth()->check())
                <x-filament-panels::avatar.user size="lg" :user="$user" />
            @else
                <x-filament-panels::avatar.guest size="lg" />
            @endif

            <div class="flex-1">
                <h2
                    class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white"
                >
                    {{ __('filament-panels::widgets/account-widget.welcome', ['app' => config('app.name')]) }}
                </h2>

                @if (filament()->auth()->check())
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ filament()->getUserName($user) }}
                    </p>
                @endif
            </div>

            @if (filament()->auth()->check())
                <form
                    action="{{ filament()->getLogoutUrl() }}"
                    method="post"
                    class="my-auto -me-2.5 sm:me-0"
                >
                    @csrf

                    <x-filament::button
                        color="gray"
                        icon="heroicon-m-arrow-left-on-rectangle"
                        icon-alias="panels::widgets.account.logout-button"
                        labeled-from="sm"
                        tag="button"
                        type="submit"
                    >
                        {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
                    </x-filament::button>
                </form>
            @else
                <x-filament::button
                    color="gray"
                    :href="filament()->getLoginUrl()"
                    icon="heroicon-m-arrow-right-on-rectangle"
                    icon-alias="panels::widgets.account.login-button"
                    tag="a"
                >
                    {{ __('filament-panels::widgets/account-widget.actions.login.label') }}
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
