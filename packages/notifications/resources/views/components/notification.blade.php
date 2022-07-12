<div
    class="pointer-events-auto mb-4 flex w-full gap-3 rounded-lg border border-neutral-200 bg-white p-4 shadow-lg transition duration-300 dark:border-neutral-700 dark:bg-neutral-900"
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
                'text-success-400' => $getStatus() === 'success',
                'text-warning-400' => $getStatus() === 'warning',
                'text-danger-400' => $getStatus() === 'danger',
            ])"
        />
    @endif
    <div class="grid flex-1">
        <span class="font-medium">{{ $getTitle() }}</span>
        <p class="text-neutral-500 dark:text-neutral-400">{{ $getDescription() }}</p>
    </div>
    <x-heroicon-s-x class="h-6 w-5 text-neutral-400 dark:text-neutral-500" x-on:click="close" />
</div>
