@props([
    'actions',
    'alignment' => null,
    'fullWidth' => false,
])

@if (count($actions))
    <div
        @if ($this->areFormActionsSticky())
            x-data="{
                isSticky: false,

                evaluatePageScrollPosition: function () {
                    this.isSticky =
                        window.scrollY + window.innerHeight * 2 <=
                        document.body.scrollHeight
                },
            }"
            x-init="evaluatePageScrollPosition"
            x-on:scroll.window="evaluatePageScrollPosition"
            x-bind:class="{
                'filament-form-actions-sticky-panel sticky bottom-0 -mx-4 transform bg-white p-4 shadow-lg transition ring-1 ring-gray-950/5 md:-translate-y-4 md:rounded-xl dark:bg-gray-800 dark:ring-white/20':
                    isSticky,
            }"
        @endif
    >
        <x-filament-actions::actions
            :actions="$actions"
            :alignment="$alignment ?? $this->getFormActionsAlignment()"
            :full-width="$fullWidth"
            class="filament-form-actions"
        />
    </div>
@endif
