@props([
    'icon',
    'theme',
])

@php
    use Filament\View\PanelsIconAlias;

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
    x-bind:aria-pressed="theme === @js($theme) ? 'true' : 'false'"
    x-bind:class="{ 'fi-active': theme === @js($theme) }"
    class="fi-theme-switcher-btn"
>
    {{
        \Filament\Support\generate_icon_html($icon, alias: match ($theme) {
            'light' => PanelsIconAlias::THEME_SWITCHER_LIGHT_BUTTON,
            'dark' => PanelsIconAlias::THEME_SWITCHER_DARK_BUTTON,
            'system' => PanelsIconAlias::THEME_SWITCHER_SYSTEM_BUTTON,
        })
    }}
</button>
