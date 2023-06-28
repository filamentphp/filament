@props([
    'actions',
    'fullWidth' => false,
])

<div
    @if (config('filament.layout.forms.actions.are_sticky'))
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
            'filament-form-actions-sticky-panel sticky bottom-0 -mx-4 transform md:-translate-y-4 bg-white p-4 shadow-lg transition ring-1 ring-black/5 md:rounded-xl':
                isSticky,
            'dark:bg-gray-800': @js(config('filament.dark_mode')) && isSticky,
        }"
    @endif
>
    <x-filament::pages.actions
        :actions="$actions"
        :alignment="config('filament.layout.forms.actions.alignment')"
        :full-width="$fullWidth"
        class="filament-form-actions"
    />
</div>
