@php
    use Filament\Support\Enums\Alignment;

    $isDisabled = $isDisabled();
    $state = $getState();
    $type = $getType();
@endphp

<div
    x-data="{
        error: undefined,

        isEditing: false,

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
                })
            })
        }
    "
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-text-input',
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
    >
        {{-- format-ignore-start --}}
        <x-filament::input
            :disabled="$isDisabled"
            :input-mode="$getInputMode()"
            :placeholder="$getPlaceholder()"
            :step="$getStep()"
            :type="$type"
            :x-bind:disabled="$isDisabled ? null : 'isLoading'"
            x-model="state"
            x-on:blur="isEditing = false"
            x-on:focus="isEditing = true"
            :attributes="
                \Filament\Support\prepare_inherited_attributes(
                    $getExtraInputAttributeBag()
                        ->merge([
                            'x-on:change' . ($type === 'number' ? '.debounce.1s' : null) => '
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
                            ',
                        ])
                        ->class([
                            match ($getAlignment()) {
                                Alignment::Center, 'center' => 'text-center',
                                Alignment::End, 'end' => 'text-end',
                                Alignment::Left, 'left' => 'text-left',
                                Alignment::Right, 'right' => 'text-right',
                                Alignment::Start, 'start', null => 'text-start',
                            },
                        ])
                )
            "
        />
        {{-- format-ignore-end --}}
    </x-filament::input.wrapper>
</div>
