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
                'fi-form-actions-sticky-panel sticky bottom-0 -mx-4 transform bg-white p-4 shadow-lg ring-1 ring-gray-950/5 transition dark:bg-gray-900 dark:ring-white/10 md:bottom-4 md:rounded-xl':
                    isSticky,
            }"
        @endif
    >
        <x-filament-actions::actions
            :actions="$actions"
            :alignment="$alignment ?? $this->getFormActionsAlignment()"
            :full-width="$fullWidth"
            class="fi-form-actions"
        />
    </div>
@endif
