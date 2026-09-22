<script setup>
import Radio from '../../../packages/support/resources/js/vue/Radio.vue'
defineProps(['settings', 'cases', 'form', 'reportChange'])
</script>

<template>
    <label v-for="(attributes, index) in cases" :key="index">
        <Radio
            v-bind="attributes"
            :form="form"
            :valid="settings.valid"
            @change="reportChange"
        />
        {{ attributes.name }} {{ attributes.value }}
    </label>
    <label v-for="value in ['standard', 'express']" :key="value">
        <Radio
            v-model="settings.delivery"
            :value="value"
            name="controlled"
            :form="form"
            :valid="settings.valid"
            @change="
                (event) => {
                    event.currentTarget.dataset.modelAtChange =
                        settings.delivery
                    reportChange(event)
                }
            "
        />
        {{ value }}
    </label>
    <output :data-model="settings.delivery">{{ settings.delivery }}</output>
</template>
