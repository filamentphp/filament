<div
    class="pointer-events-auto mb-4 flex w-full gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-lg transition duration-300 dark:border-gray-700 dark:bg-gray-900"
    x-data="notificationComponent({ notification: {{ Js::from($toLivewire()) }} })"
    x-show="show"
    x-transition:enter-start="translate-x-12 opacity-0"
    x-transition:leave-end="scale-95 opacity-0"
    x-bind:class="{ 'absolute': !show }"
    id="notification-{{ $getId() }}"
    wire:key="notification-{{ $getId() }}"
>
    @if ($getIcon())
        <x-dynamic-component
            :component="$getIcon()"
            :class="Arr::toCssClasses([
                'h-6 w-6',
                match ($getIconColor()) {
                    'success' => 'text-success-400',
                    'warning' => 'text-warning-400',
                    'danger' => 'text-danger-400',
                    'primary' => 'text-primary-400',
                    'secondary' => 'text-gray-400',
                },
            ])"
        />
    @endif

    <div class="grid flex-1">
        <span class="font-medium">{{ $getTitle() }}</span>

        @if ($getDescription())
            <p class="text-gray-500 dark:text-gray-400">{{ $getDescription() }}</p>
        @endif

        @if ($getActions())
            <div class="flex gap-3 mt-4">
                @foreach($getActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        @endunless
    </div>

    <x-heroicon-s-x class="h-6 w-5 text-gray-400 dark:text-gray-500" x-on:click="close" />
</div>
