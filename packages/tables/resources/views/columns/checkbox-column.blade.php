@php
    $isDisabled = $isDisabled();
    $state = (bool) $getState();
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

                        const newState = $refs.newState.value === '1' ? true : false

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
                'fi-ta-checkbox flex items-center',
                'px-3 py-4' => ! $isInline(),
            ])
    }}
>
    <input type="hidden" value="{{ $state ? 1 : 0 }}" x-ref="newState" />

    <x-filament::input.checkbox
        alpine-valid="! error"
        :disabled="$isDisabled"
        :x-bind:disabled="$isDisabled ? null : 'isLoading'"
        x-model="state"
        x-on:change="
            isLoading = true

            const response = await $wire.updateTableColumnState(
                name,
                recordKey,
                $event.target.checked,
            )

            error = response?.error ?? undefined

            isLoading = false
        "
        x-tooltip="
            error === undefined
                ? false
                : {
                    content: error,
                    theme: $store.theme,
                }
        "
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraInputAttributes(), escape: false)
        "
    />
</div>
