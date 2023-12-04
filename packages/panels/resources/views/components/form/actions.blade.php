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
                isStuck: false,
                evaluatePageScrollPosition: function () {
                    this.isSticky = this.isStuck == false && window.scrollY >= 0
                    this.isStuck = $el.getBoundingClientRect().bottom <= window.innerHeight
                },
            }"
            x-init="evaluatePageScrollPosition"
            x-intersect.threshold.100="isStuck = true"
            x-on:scroll.window="evaluatePageScrollPosition"
            x-bind:class="{
                'transform bg-white shadow-lg ring-1 ring-gray-950/5 transition dark:bg-gray-900 dark:ring-white/10 md:rounded-t-xl -mx-4 md:mx-0':
                    isSticky,
                'fi-sticky sticky p-4': true,
            }"
            x-bind:style="{
                bottom: '-1px',
            }"
        @endif
        class="fi-form-actions"
    >
        <x-filament-actions::actions
            :actions="$actions"
            :alignment="$alignment ?? $this->getFormActionsAlignment()"
            :full-width="$fullWidth"
        />
    </div>
@endif
