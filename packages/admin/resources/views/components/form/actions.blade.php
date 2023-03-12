@props([
    'actions',
    'fullWidth' => false,
])

<div
    @if (config('filament.layout.forms.actions.are_sticky'))
        x-data="{
        
            areSticky: false,
            
            evaluatePageScrollPosition: function() {
                let documentHeight = document.body.scrollHeight
                let currentScroll = window.scrollY + window.innerHeight
                
                this.areSticky = (currentScroll + 200) <= documentHeight
            },
            
        }"
        x-init="evaluatePageScrollPosition"
        x-on:scroll.window="evaluatePageScrollPosition"
        class="transform"
        x-bind:class="{
            'sticky left-0 bottom-0 mx-auto max-w-2xl -translate-y-4 rounded-md border border-gray-300 bg-white p-3 shadow-3xl transition-colors transition-shadow transition-transform': areSticky,
            'dark:border-gray-700 dark:bg-gray-800 dark:shadow-none': @js(config('filament.dark_mode')) && areSticky,
            'translate-y-0': ! areSticky
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
