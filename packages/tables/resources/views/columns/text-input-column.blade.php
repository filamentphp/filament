@php
    $alignClass = match ($getAlignment()) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $state = $getState();
@endphp

<div
    x-data="{
        error: undefined,
        state: @js($state),
        isLoading: false,
        isEditing: false,
    }"
    x-init="
        Livewire.hook('message.processed', (component) => {
            if (component.component.id !== @js($this->id)) {
                return
            }

            if (isEditing) {
                return
            }

            if (! $refs.newState) {
                return
            }

            let newState = $refs.newState.value

            if (state === newState) {
                return
            }

            state = newState
        })
    "
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class(['filament-tables-text-input-column'])
    }}
>
    <input
        type="hidden"
        value="{{ \Illuminate\Support\Str::of($state)->replace('"', '\\"') }}"
        x-ref="newState"
    />

    <input
        x-model="state"
        type="{{ $getType() }}"
        {!! $isDisabled() ? 'disabled' : null !!}
        {!! ($inputMode = $getInputMode()) ? "inputmode=\"{$inputMode}\"" : null !!}
        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
        {!! ($interval = $getStep()) ? "step=\"{$interval}\"" : null !!}
        x-on:focus="isEditing = true"
        x-on:blur="isEditing = false"
        x-on:change{{ $getType() === 'number' ? '.debounce.1s' : null }}="
            isLoading = true
            response = await $wire.updateTableColumnState(
                @js($getName()),
                @js($recordKey),
                $event.target.value,
            )
            error = response?.error ?? undefined
            if (! error) state = response
            isLoading = false
        "
        :readonly="isLoading"
        x-tooltip="error"
        {{
            $attributes
                ->merge($getExtraInputAttributes())
                ->merge($getExtraAttributes())
                ->class([
                    'ml-0.5 inline-block rounded-lg text-gray-900 shadow-sm outline-none transition duration-75 read-only:opacity-50 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70',
                    $alignClass,
                    'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
                ])
        }}
        x-bind:class="{
            'border-gray-300': ! error,
            'dark:border-gray-600': ! error && @js(config('forms.dark_mode')),
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
    />
</div>
