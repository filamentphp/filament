@props([
    'actions',
    'fullWidth' => false,
])

<div
    @if (config('filament.layout.forms.actions.are_sticky'))
        x-data="{
            sticky: false,
            scrollPage() {
                let documentHeight = document.body.scrollHeight;
                let currentScroll = window.scrollY + window.innerHeight;
                let modifier = 200;
                if (currentScroll + modifier > documentHeight) {
                    this.sticky = false
                } else {
                    this.sticky = true
                }
            }
        }"
        x-on:scroll.window="scrollPage"
        x-init="scrollPage"
        class="transform"
        x-bind:class="{
            'sticky left-0 bottom-0 mx-auto max-w-2xl -translate-y-4 rounded-md border border-gray-300 bg-white p-3 shadow-3xl transition-colors transition-shadow transition-transform': sticky,
            'dark:border-gray-700 dark:bg-gray-800 dark:shadow-none': !!{{ config('filament.dark_mode') }} && sticky,
            'translate-y-0': !sticky
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
