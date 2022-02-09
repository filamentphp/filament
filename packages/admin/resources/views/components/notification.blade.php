<div
    x-data="{
        show: false,
        init() {
            this.$nextTick(() => this.show = true)

            setTimeout(() => this.transitionOut(), 6000)
        },
        transitionOut() {
            this.show = false

            setTimeout(() => this.remove(this.notification), 500)
        },
        iconClasses: {
            ['x-bind:class']() {
                return {
                    'text-danger-600': notification.status === 'danger',
                    'text-success-600': notification.status === 'success',
                    'text-warning-600': notification.status === 'warning',
                    'text-primary-600': ! ['danger', 'success', 'warning'].includes(notification.status),
                }
            }
        }
    }"
    x-show="show"
    x-transition:enter.duration.500ms
    {{ $attributes->class(['flex flex-col h-auto sm:max-w-xs max-w-screen mx-auto space-y-2 pointer-events-auto filament-notification']) }}
>
    <div
        x-show="show"
        x-transition
        class="flex items-start px-3 py-2 space-x-2 rtl:space-x-reverse text-xs shadow ring-1 rounded-xl"
        x-bind:class="{
            'bg-danger-50 ring-danger-200': notification.status === 'danger',
            'bg-success-50 ring-success-200': notification.status === 'success',
            'bg-warning-50 ring-warning-200': notification.status === 'warning',
            '{{ \Illuminate\Support\Arr::toCssClasses([
                'bg-white ring-gray-200',
                'dark:bg-gray-700 dark:ring-gray-600' => config('filament.dark_mode'),
            ]) }}': ! ['warning', 'success', 'danger'].includes(notification.status),
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
                        'text-danger-900': notification.status === 'danger',
                        'text-success-900': notification.status === 'success',
                        'text-warning-900': notification.status === 'warning',
                        '{{ \Illuminate\Support\Arr::toCssClasses([
                            'text-gray-900',
                            'dark:text-gray-200' => config('filament.dark_mode'),
                        ]) }}': ! ['warning', 'success', 'danger'].includes(notification.status)
                    }"
                    x-text="notification.message"
                >
                </p>
            </div>
        </div>
    </div>
</div>
