@props([
    'icon',
    'theme',
])

@php
    $label = __("filament-panels::layout.actions.theme_switcher.{$theme}.label");
@endphp

<button
    aria-label="{{ $label }}"
    type="button"
    x-on:click="(theme = @js($theme)) && close()"
    x-tooltip="{
        content: @js($label),
        theme: $store.theme,
    }"
    x-bind:class="{ 'fi-active': theme === @js($theme) }"
    class="fi-theme-switcher-btn"
>
    {{ \Filament\Support\generate_icon_html($icon, alias: "panels::theme-switcher.{$theme}-button") }}
</button>
