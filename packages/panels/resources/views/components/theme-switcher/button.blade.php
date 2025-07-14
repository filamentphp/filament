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
    {{
        \Filament\Support\generate_icon_html($icon, alias: match ($theme) {
            'light' => \Filament\View\PanelsIconAlias::THEME_SWITCHER_LIGHT_BUTTON,
            'dark' => \Filament\View\PanelsIconAlias::THEME_SWITCHER_DARK_BUTTON,
            'system' => \Filament\View\PanelsIconAlias::THEME_SWITCHER_SYSTEM_BUTTON,
        })
    }}
</button>
