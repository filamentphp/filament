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
        }"
    @endif
    {{
        $attributes->class([
            'fi-ta-row transition duration-75',
            'hover:bg-gray-50 dark:hover:bg-white/5' => $recordAction || $recordUrl,
            $stripedClasses => $striped,
        ])
    }}
>
    {{ $slot }}
</tr>
