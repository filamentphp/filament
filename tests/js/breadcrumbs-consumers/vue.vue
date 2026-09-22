<script setup lang="ts">
import Breadcrumbs from '../../../packages/support/resources/js/vue/Breadcrumbs.vue'

type Props = InstanceType<typeof Breadcrumbs>['$props']
// @ts-expect-error `label` is required.
const missingLabel: Props = { breadcrumbs: [{ href: '/' }] }
// @ts-expect-error `href` must be a string.
const invalidHref: Props = { breadcrumbs: [{ label: 'Home', href: 42 }] }
void missingLabel
void invalidHref
</script>
<template>
    <Breadcrumbs
        aria-label="Location"
        :breadcrumbs="[
            {
                label: 'Home',
                href: '/',
                target: '_blank',
                'data-testid': 'home-link',
                'data-intercept': true,
                onClick: (event) => event.preventDefault(),
            },
            { label: 'Current' },
        ]"
    >
        <template #separator>/</template>
        <template #separatorRtl>\</template>
    </Breadcrumbs>
</template>
