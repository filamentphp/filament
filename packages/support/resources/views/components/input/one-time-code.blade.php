@props([
    'length' => 6,
])

<div
    x-data="{ currentNumberOfDigits: null }"
    {{
        $attributes
            ->class([
                'fi-one-time-code-input-ctn',
            ])
    }}
>
    @foreach (range(1, $length) as $digit)
        <div
            x-bind:class="{
                'fi-active':
                    currentNumberOfDigits !== null &&
                    currentNumberOfDigits >= {{ $digit }},
            }"
            class="fi-one-time-code-input-digit-field"
        ></div>
    @endforeach

    <input
        autocomplete="one-time-code"
        inputmode="numeric"
        minlength="{{ $length }}"
        maxlength="{{ $length }}"
        pattern="\d{{ '{' . $length . '}' }}"
        type="text"
        x-data="{}"
        x-on:focus="currentNumberOfDigits = $el.value.length"
        x-on:blur="currentNumberOfDigits = null"
        x-on:input="
            $el.value = $el.value.replace(/\D/g, '')
            currentNumberOfDigits = $el.value.length
        "
        x-bind:class="{ 'fi-valid': currentNumberOfDigits >= {{ $length }} }"
        class="fi-one-time-code-input"
    />
</div>
