<div
    x-data="{
        isVisible: false,
        init: function () {
            this.$nextTick(() => this.isVisible = true)

            setTimeout(() => this.transitionOut(), 6000)
        },
        transitionOut: function () {
            this.isVisible = false

            setTimeout(() => this.remove(this.notification), 500)
        },
        iconClasses: {
            ['x-bind:class']() {
                return {
                    'text-danger-600 @if (config('filament.dark_mode')) dark:text-danger-400 @endif': notification.status === 'danger',
                    'text-success-600 @if (config('filament.dark_mode')) dark:text-success-400 @endif': notification.status === 'success',
                    'text-warning-600 @if (config('filament.dark_mode')) dark:text-warning-400 @endif': notification.status === 'warning',
                    'text-primary-600 @if (config('filament.dark_mode')) dark:text-primary-400 @endif': ! ['danger', 'success', 'warning'].includes(notification.status),
                }
            }
        },
    }"
    x-show="isVisible"
    x-transition:enter.duration.500ms
    {{ $attributes->class(['flex flex-col h-auto sm:w-80 max-w-screen space-y-2 pointer-events-auto filament-notification']) }}
>
    <div
        x-show="isVisible"
        x-transition
        class="flex items-start px-3 py-2 space-x-2 backdrop-blur-xl backdrop-saturate-150 rtl:space-x-reverse text-xs shadow ring-1 rounded-xl"
        x-bind:class="{
            'bg-danger-50/50 ring-danger-200 @if (config('filament.dark_mode')) dark:bg-danger-900/50 dark:ring-danger-600 @endif': notification.status === 'danger',
            'bg-success-50/50 ring-success-200 @if (config('filament.dark_mode')) dark:bg-success-900/50 dark:ring-success-600 @endif': notification.status === 'success',
            'bg-warning-50/50 ring-warning-200 @if (config('filament.dark_mode')) dark:bg-warning-900/50 dark:ring-warning-600 @endif': notification.status === 'warning',
            'bg-white/50 ring-gray-200 @if (config('filament.dark_mode')) dark:bg-gray-900/50 dark:ring-gray-600 @endif': ! ['warning', 'success', 'danger'].includes(notification.status),
        }"
    >
        <x-heroicon-o-x-circle class="shrink-0 w-6 h-6" x-show="notification.status === 'danger'" x-bind="iconClasses" />
        <x-heroicon-o-check-circle class="shrink-0 w-6 h-6" x-show="notification.status === 'success'" x-bind="iconClasses" />
        <x-heroicon-o-exclamation class="shrink-0 w-6 h-6" x-show="notification.status === 'warning'" x-bind="iconClasses" />
        <x-heroicon-o-information-circle class="shrink-0 w-6 h-6" x-show="! ['danger', 'success', 'warning'].includes(notification.status)" x-bind="iconClasses" />

        <div class="flex-1 space-y-1">
            <div class="flex items-center justify-between font-medium">
                <p
                    class="text-sm leading-6"
                    x-bind:class="{
                        'text-danger-900 @if (config('filament.dark_mode')) dark:text-danger-200 @endif': notification.status === 'danger',
                        'text-success-900 @if (config('filament.dark_mode')) dark:text-success-200 @endif': notification.status === 'success',
                        'text-warning-900 @if (config('filament.dark_mode')) dark:text-warning-200 @endif': notification.status === 'warning',
                        'text-gray-900 @if (config('filament.dark_mode')) dark:text-gray-200 @endif': ! ['warning', 'success', 'danger'].includes(notification.status)
                    }"
                    x-text="notification.message"
                ></p>
            </div>
        </div>
    </div>
</div>
