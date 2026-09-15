<script setup>
import { ref, computed } from 'vue'
const props = defineProps([
    'value',
    'config',
    'id',
    'ariaDescribedBy',
    'disabled',
    'readOnly',
    'required',
    'invalid',
    'onChange',
    'onBlur',
    'utilities',
])
const report = ref('')
const locked = computed(() => props.disabled || props.readOnly)
function inspect() {
    const utilities = props.utilities
    report.value = JSON.stringify({
        path: utilities.$statePath,
        state: utilities.$state,
        sibling: utilities.$get('caption'),
        root: utilities.$get('data.caption', true),
        relative: utilities.$get('../../caption'),
    })
    utilities.$set('caption', 'Changed by renderer')
    utilities.$set('data.caption', 'Changed root by renderer', true, true)
}
</script>

<template>
    <div>
        <input
            :id="id"
            aria-label="Title"
            :aria-describedby="ariaDescribedBy"
            :value="value.title"
            :disabled="disabled"
            :readonly="readOnly"
            :required="required"
            :aria-invalid="invalid"
            @input="onChange({ ...value, title: $event.target.value })"
            @blur="onBlur"
        />
        <label
            ><input
                type="checkbox"
                :checked="value.enabled"
                :disabled="locked"
                @change="onChange({ ...value, enabled: $event.target.checked })"
                @blur="onBlur"
            />Enabled</label
        >
        <button
            type="button"
            :disabled="locked"
            @click="
                onChange({
                    ...value,
                    tags: value.tags.includes('sms') ? [] : ['sms', 'push'],
                })
            "
            @blur="onBlur"
        >
            Toggle channels
        </button>
        <output data-channels>{{ JSON.stringify(value.tags) }}</output>
        <output data-config>{{ JSON.stringify(config) }}</output>
        <button type="button" :disabled="locked" @click="inspect">
            Use utilities
        </button>
        <output data-report>{{ report }}</output>
        <button
            type="button"
            @click="
                async () => {
                    const { $replaceTitle } = utilities
                    report = JSON.stringify(
                        await $replaceTitle({
                            title: 'PHP café replacement',
                        }),
                    )
                }
            "
        >
            Call field method
        </button>
        <button
            type="button"
            @click="
                async () => {
                    report = JSON.stringify(
                        await utilities.$callSchemaComponentMethod(
                            'inspectTitle',
                            { prefix: 'Read: ' },
                        ),
                    )
                }
            "
        >
            Call renderless method
        </button>
        <button
            type="button"
            @click="
                async () => {
                    report = JSON.stringify(
                        await utilities.$callSchemaComponentMethod(
                            'unexposedMethod',
                        ),
                    )
                }
            "
        >
            Call unexposed method
        </button>
        <button
            type="button"
            @click="
                async () => {
                    report = JSON.stringify(
                        await utilities.$wire.$call(
                            'changeCaption',
                            'Via $wire café',
                        ),
                    )
                }
            "
        >
            Call Livewire method
        </button>
    </div>
</template>
