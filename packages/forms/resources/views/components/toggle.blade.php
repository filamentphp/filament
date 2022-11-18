<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $isInline = $isInline();
        $statePath = $getStatePath();
    @endphp

    @if ($isInline)
        <x-slot name="labelPrefix">
    @endif
            <button
                x-data="{ state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }} }"
                x-bind:aria-checked="state.toString()"
                x-on:click="state = ! state"
                x-bind:class="{
                    '{{ match ($getOnColor()) {
                        'danger' => 'bg-danger-500',
                        'gray' => 'bg-gray-500',
                        'secondary' => 'bg-secondary-500',
                        'success' => 'bg-success-500',
                        'warning' => 'bg-warning-500',
                        default => 'bg-primary-600',
                    } }}': state,
                    '{{ match ($getOffColor()) {
                        'danger' => 'bg-danger-500',
                        'primary' => 'bg-primary-500',
                        'secondary' => 'bg-secondary-500',
                        'success' => 'bg-success-500',
                        'warning' => 'bg-warning-500',
                        default => 'bg-gray-200',
                    } }} dark:bg-white/10': ! state,
                }"
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
                        ->class(['filament-forms-toggle-component relative inline-flex border-2 border-transparent shrink-0 h-6 w-11 rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none disabled:opacity-70 disabled:pointer-events-none'])
                }}
            >
                <span
                    class="pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 ease-in-out transition duration-200"
                    x-bind:class="{
                        'translate-x-5 rtl:-translate-x-5': state,
                        'translate-x-0': ! state,
                    }"
                >
                    <span
                        class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                        aria-hidden="true"
                        x-bind:class="{
                            'opacity-0 ease-out duration-100': state,
                            'opacity-100 ease-in duration-200': ! state,
                        }"
                    >
                        @if ($hasOffIcon())
                            <x-filament::icon
                                :name="$getOffIcon()"
                                alias="filament-forms::components.toggle.off"
                                :color="match ($getOffColor()) {
                                    'danger' => 'text-danger-500',
                                    'primary' => 'text-primary-500',
                                    'secondary' => 'text-secondary-500',
                                    'success' => 'text-success-500',
                                    'warning' => 'text-warning-500',
                                    default => 'text-gray-400',
                                }"
                                size="h-3 w-3"
                            />
                        @endif
                    </span>

                    <span
                        class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                        aria-hidden="true"
                        x-bind:class="{
                            'opacity-100 ease-in duration-200': state,
                            'opacity-0 ease-out duration-100': ! state,
                        }"
                    >
                        @if ($hasOnIcon())
                            <x-filament::icon
                                :name="$getOnIcon()"
                                alias="filament-forms::components.toggle.on"
                                :color="match ($getOnColor()) {
                                    'danger' => 'text-danger-500',
                                    'gray' => 'text-gray-400',
                                    'secondary' => 'text-secondary-500',
                                    'success' => 'text-success-500',
                                    'warning' => 'text-warning-500',
                                    default => 'text-primary-500',
                                }"
                                size="h-3 w-3"
                                x-cloak
                            />
                        @endif
                    </span>
                </span>
            </button>
    @if ($isInline)
        </x-slot>
    @endif
</x-dynamic-component>
