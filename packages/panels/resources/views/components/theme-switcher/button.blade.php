@props([
    'icon',
    'theme',
])

@php
    $label = __("filament::layout.actions.theme_switcher.{$theme}.label");
@endphp

<button
    aria-label="{{ $label }}"
    type="button"
    x-bind:class="
        theme === @js($theme)
            ? 'bg-gray-950/5 text-primary-500 dark:bg-white/5 dark:text-primary-400'
            : 'text-gray-400 hover:text-gray-500 focus:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:focus:text-gray-400'
    "
    x-on:click="(theme = @js($theme)) && close()"
    x-tooltip="@js($label)"
    class="flex justify-center rounded-lg p-2 outline-none transition duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5"
>
    <x-filament::icon
        :name="$icon"
        :alias="'panels::topbar.user-menu.theme-switcher.' . $theme . '-button'"
        class="h-5 w-5"
    />
</button>
