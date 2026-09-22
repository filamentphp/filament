<script setup>
import Checkbox from '../../../packages/support/resources/js/vue/Checkbox.vue'
defineProps(['settings', 'cases', 'form', 'reportChange'])
</script>

<template>
    <label v-for="attributes in cases" :key="attributes.name">
        <Checkbox
            v-bind="attributes"
            :form="form"
            :valid="settings.valid"
            @change="reportChange"
        />
        {{ attributes.name }}
    </label>
    <label>
        <Checkbox
            v-model="settings.checked"
            :valid="settings.valid"
            :form="form"
            name="controlled"
            value="enabled"
            @change="
                (event) => {
                    event.currentTarget.dataset.modelAtChange = String(
                        settings.checked,
                    )
                    reportChange(event)
                }
            "
        />
        Controlled
    </label>
    <output :data-model="String(settings.checked)">{{
        String(settings.checked)
    }}</output>
</template>
