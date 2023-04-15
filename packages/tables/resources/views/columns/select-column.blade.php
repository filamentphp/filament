@php
    $isDisabled = $isDisabled();
    $state = $getState();
@endphp

<div
    x-data="{
        error: undefined,
        state: @js($state ?? ''),
        isLoading: false,
    }"
    x-init="
        Livewire.hook('message.processed', (component) => {
            if (component.component.id !== @js($this->id)) {
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
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class([
        'filament-tables-select-column',
    ]) }}
>
    <input
        type="hidden"
        value="{{ str($state)->replace('"', '\\"') }}"
        x-ref="newState"
    />

    <select
        x-model="state"
        x-on:change="
            isLoading = true
            response = await $wire.updateTableColumnState(@js($getName()), @js($recordKey), $event.target.value)
            error = response?.error ?? undefined
            if (! error) state = response
            isLoading = false
        "
        @if (! $isDisabled)
            x-bind:disabled="isLoading"
        @endif
        x-tooltip="error"
        x-bind:class="{
            'border-gray-300 dark:border-gray-600': ! error,
            'border-danger-600 ring-1 ring-inset ring-danger-600 dark:border-danger-400 dark:ring-danger-400': error,
        }"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraInputAttributes(), escape: false)
                ->merge([
                    'disabled' => $isDisabled,
                ])
                ->class(['ml-0.5 text-gray-900 inline-block transition duration-75 rounded-lg shadow-sm outline-none sm:text-sm focus:ring-primary-500 focus:ring-1 focus:ring-inset focus:border-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500'])
        }}
    >
        @if ($canSelectPlaceholder())
            <option value="">{{ $getPlaceholder() }}</option>
        @endif

        @foreach ($getOptions() as $value => $label)
            <option
                value="{{ $value }}"
                @disabled($isOptionDisabled($value, $label))
            >
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
