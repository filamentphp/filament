@props([
    'actions',
    'alignment' => null,
    'fullWidth' => false,
])

<div
    @if ($this->areFormActionsSticky())
        x-data="{

            isSticky: false,

            evaluatePageScrollPosition: function() {
                let documentHeight = document.body.scrollHeight
                let currentScroll = window.scrollY + window.innerHeight

                this.isSticky = (currentScroll + window.innerHeight) <= documentHeight
            },

        }"
        x-init="evaluatePageScrollPosition"
        x-on:scroll.window="evaluatePageScrollPosition"
        x-bind:class="{
            'filament-form-actions-sticky-panel sticky bottom-0 -mx-4 transform bg-white p-4 shadow-lg transition ring-1 ring-black/5 md:-translate-y-4 md:rounded-xl dark:bg-gray-800': isSticky,
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
