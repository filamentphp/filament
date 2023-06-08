<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $offColor = $getOffColor();
        $onColor = $getOnColor();
        $statePath = $getStatePath();
    @endphp

    @capture($content)
        <button
            x-data="{ state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }} }"
            x-bind:aria-checked="state?.toString()"
            x-on:click="state = ! state"
            x-bind:class="
                state
                    ? '{{
                        match ($onColor) {
                            'danger' => 'bg-danger-600',
                            'gray' => 'bg-gray-600',
                            'info' => 'bg-info-600',
                            'primary', null => 'bg-primary-600',
                            'secondary' => 'bg-secondary-600',
                            'success' => 'bg-success-600',
                            'warning' => 'bg-warning-600',
                            default => $onColor,
                        }
                    }}'
                    : '{{
                        match ($offColor) {
                            'danger' => 'bg-danger-600',
                            'gray' => 'bg-gray-600',
                            'info' => 'bg-info-600',
                            'primary' => 'bg-primary-600',
                            'secondary' => 'bg-secondary-600',
                            'success' => 'bg-success-600',
                            'warning' => 'bg-warning-600',
                            null => 'bg-gray-200 dark:bg-gray-700',
                            default => $offColor,
                        }
                    }}'
            "
            {{
                $attributes
                    ->merge([
                        'aria-checked' => 'false',
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'dusk' => "filament.forms.{$statePath}",
                        'id' => $getId(),
                        'role' => 'switch',
                        'type' => 'button',
                        'wire:loading.attr' => 'disabled',
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->class(['filament-forms-toggle-component relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent outline-none transition-colors duration-200 ease-in-out disabled:pointer-events-none disabled:opacity-70'])
            }}
        >
            <span
                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                x-bind:class="{
                    'translate-x-5 rtl:-translate-x-5': state,
                    'translate-x-0': ! state,
                }"
            >
                <span
                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-0 ease-out duration-100': state,
                        'opacity-100 ease-in duration-200': ! state,
                    }"
                >
                    @if ($hasOffIcon())
                        <x-filament::icon
                            :name="$getOffIcon()"
                            alias="forms::components.toggle.off"
                            :color="match ($offColor) {
                                'danger' => 'text-danger-600',
                                'gray' => 'text-gray-600',
                                'info' => 'text-info-600',
                                'primary' => 'text-primary-600',
                                'secondary' => 'text-secondary-600',
                                'success' => 'text-success-600',
                                'warning' => 'text-warning-600',
                                null => 'text-gray-400 dark:text-gray-700',
                                default => $offColor,
                            }"
                            size="h-3 w-3"
                        />
                    @endif
                </span>

                <span
                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                    aria-hidden="true"
                    x-bind:class="{
                        'opacity-100 ease-in duration-200': state,
                        'opacity-0 ease-out duration-100': ! state,
                    }"
                >
                    @if ($hasOnIcon())
                        <x-filament::icon
                            :name="$getOnIcon()"
                            alias="forms::components.toggle.on"
                            :color="match ($onColor) {
                                'danger' => 'text-danger-600',
                                'gray' => 'text-gray-600',
                                'info' => 'text-info-600',
                                'primary', null => 'text-primary-600',
                                'secondary' => 'text-secondary-600',
                                'success' => 'text-success-600',
                                'warning' => 'text-warning-600',
                                default => $onColor,
                            }"
                            size="h-3 w-3"
                            x-cloak="x-cloak"
                        />
                    @endif
                </span>
            </span>
        </button>
    @endcapture

    @if ($isInline())
        <x-slot name="labelPrefix">
            {{ $content() }}
        </x-slot>
    @else
        {{ $content() }}
    @endif
</x-dynamic-component>
