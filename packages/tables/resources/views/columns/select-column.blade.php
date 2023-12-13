@php
    $canSelectPlaceholder = $canSelectPlaceholder();
    $isDisabled = $isDisabled();

    $state = $getState();
    if ($state instanceof \BackedEnum) {
        $state = $state->value;
    }
    $state = strval($state);
@endphp

<div
    x-data="{
        error: undefined,

        isLoading: false,

        name: @js($getName()),

        recordKey: @js($recordKey),

        state: @js($state),
    }"
    x-init="
        () => {
            Livewire.hook('commit', ({ component, commit, succeed, fail, respond }) => {
                succeed(({ snapshot, effect }) => {
                    $nextTick(() => {
                        if (component.id !== @js($this->getId())) {
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
                })
            })
        }
    "
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-select',
                'px-3 py-4' => ! $isInline(),
            ])
    }}
>
    <input
        type="hidden"
        value="{{ str($state)->replace('"', '\\"') }}"
        x-ref="newState"
    />

    <x-filament::input.wrapper
        :alpine-disabled="'isLoading || ' . \Illuminate\Support\Js::from($isDisabled)"
        alpine-valid="error === undefined"
        x-tooltip="
            error === undefined
                ? false
                : {
                    content: error,
                    theme: $store.theme,
                }
        "
        x-on:click.stop=""
    >
        <x-filament::input.select
            :disabled="$isDisabled"
            :x-bind:disabled="$isDisabled ? null : 'isLoading'"
            x-model="state"
            x-on:change="
                isLoading = true

                const response = await $wire.updateTableColumnState(
                    name,
                    recordKey,
                    $event.target.value,
                )

                error = response?.error ?? undefined

                if (! error) {
                    state = response
                }

                isLoading = false
            "
            :attributes="\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())"
        >
            @if ($canSelectPlaceholder)
                <option value="">{{ $getPlaceholder() }}</option>
            @endif

            @foreach ($getOptions() as $value => $label)
                <option
                    @disabled($isOptionDisabled($value, $label))
                    value="{{ $value }}"
                >
                    {{ $label }}
                </option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
