@props([
    'message',
    'status',
])

<div
    x-data="{ isVisible: false }"
    x-init="() => {
        $nextTick(() => isVisible = true)
        setTimeout(() => isVisible = false, 5000)
        setTimeout(() => $el.remove(), 6000)
    }"
    x-cloak
    class="fixed inset-x-0 bottom-0 z-10 p-3 pointer-events-none"
>
    <div class="flex flex-col w-full h-auto sm:max-w-xs ml-auto space-y-2 pointer-events-auto">
        <div
            x-show="isVisible"
            x-transition
            class="flex items-start w-full px-3 py-2 space-x-2 text-xs shadow bg-white/50 ring-gray-200 ring-1 rounded-xl backdrop-blur-xl backdrop-saturate-150"
        >
            <x-dynamic-component :component="match ($status) {
                'danger' => 'heroicon-o-x-circle',
                'success' => 'heroicon-o-check-circle',
                'warning' => 'heroicon-o-exclamation',
                default => 'heroicon-o-information-circle',
            }" :class="\Illuminate\Support\Arr::toCssClasses([
                'flex-shrink-0 w-6 h-6',
                match ($status) {
                    'danger' => 'text-danger-500',
                    'success' => 'text-success-500',
                    'warning' => 'text-warning-500',
                    default => 'text-primary-500',
                }
            ])" />

            <div class="flex-1 space-y-1">
                <div class="flex items-center justify-between font-medium">
                    <p class="text-sm leading-6 truncate">
                        {{ $message }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
