@props(['actions'])

@php
$actions = collect($actions);
@endphp

<div class="relative trov-multi-action-button" x-ref="wrapper" wire:ignore.self x-data="{
    open: false,
    toggle() {
        if (this.open) { return this.close(); } this.$refs.button.focus();
        this.open = true;
    },
    close(focusAfter) {
        if (!this.open) return;
        this.open = false;
        focusAfter && focusAfter.focus();
    }
}">
    <div x-on:keydown.escape.prevent.stop="close($refs.button)"
        x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']"
        @class([
            'inline-flex relative items-center justify-center font-medium tracking-tight rounded-xl border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button p-1',
            'text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600',
            'dark:bg-gray-800 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800' => config(
                'filament.dark_mode'
            ),
        ])>

        {{ $actions->first() }}

        <div x-ref="panel" x-show="open" x-on:click.outside="close($refs.button)" x-bind:id="$id('dropdown-button')"
            style="display: none;"
            class="absolute left-0 right-auto z-10 flex flex-col items-stretch min-w-[200px] gap-2 p-2 shadow-2xl top-[105%] md:!left-auto md:right-0 bg-white dark:bg-black/80 rounded-xl">

            @foreach ($actions->skip(1) as $action)
                {{ $action }}
            @endforeach
        </div>
        <button type="button" x-ref="button" x-on:click="toggle()" x-bind:aria-expanded="open"
            x-bind:aria-controls="$id('dropdown-button')" class="self-stretch px-2">
            <x-heroicon-o-dots-vertical class="w-4 h-4" />
        </button>
    </div>
</div>
