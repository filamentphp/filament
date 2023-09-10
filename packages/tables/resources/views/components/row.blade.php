@props([
    'alpineHidden' => null,
    'alpineSelected' => null,
    'recordAction' => null,
    'recordUrl' => null,
    'striped' => false,
])

@php
    $hasAlpineHiddenClasses = filled($alpineHidden);
    $hasAlpineSelectedClasses = filled($alpineSelected);

    $stripedClasses = 'bg-gray-50 dark:bg-white/5';
@endphp

<tr
    @if ($hasAlpineHiddenClasses || $hasAlpineSelectedClasses)
        x-bind:class="{
            {{ $hasAlpineHiddenClasses ? "'hidden': {$alpineHidden}," : null }}
            {{ $hasAlpineSelectedClasses && (! $striped) ? "'{$stripedClasses}': {$alpineSelected}," : null }}
            {{ $hasAlpineSelectedClasses ? "'[&>*:first-child]:relative [&>*:first-child]:before:absolute [&>*:first-child]:before:start-0 [&>*:first-child]:before:inset-y-0 [&>*:first-child]:before:w-0.5 [&>*:first-child]:before:bg-primary-600 [&>*:first-child]:dark:before:bg-primary-500': {$alpineSelected}," : null }}
        }"
    @endif
    {{
        $attributes->class([
            'fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75',
            'hover:bg-gray-50 dark:hover:bg-white/5' => $recordAction || $recordUrl,
            $stripedClasses => $striped,
        ])
    }}
>
    {{ $slot }}
</tr>
