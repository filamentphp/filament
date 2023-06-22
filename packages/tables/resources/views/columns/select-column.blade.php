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
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class(['filament-tables-select-column'])
    }}
>
    <input
        type="hidden"
        value="{{ \Illuminate\Support\Str::of($state)->replace('"', '\\"') }}"
        x-ref="newState"
    />

    <select
        x-model="state"
        x-on:change="
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
        @if ($isDisabled)
            disabled
        @else
            x-bind:disabled="isLoading"
        @endif
        x-tooltip="error"
        {{
            $attributes
                ->merge($getExtraInputAttributes())
                ->merge($getExtraAttributes())
                ->class([
                    'ml-0.5 inline-block rounded-lg text-gray-900 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70',
                    'dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
                ])
        }}
        x-bind:class="{
            'border-gray-300': ! error,
            'dark:border-gray-600': ! error && @js(config('forms.dark_mode')),
            'border-danger-600 ring-1 ring-inset ring-danger-600': error,
        }"
    >
        @unless ($isPlaceholderSelectionDisabled())
            <option value="">{{ $getPlaceholder() }}</option>
        @endif

        @foreach ($getOptions() as $value => $label)
            <option
                value="{{ $value }}"
                {!! $isOptionDisabled($value, $label) ? 'disabled' : null !!}
            >
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
