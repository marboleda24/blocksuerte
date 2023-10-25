<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <jet-section-title>
            <template #title><slot name="title"></slot></template>
            <template #description><slot name="description"></slot></template>
        </jet-section-title>

        <div class="intro-y box mt-5 md:mt-0 md:col-span-2">
            <div class="p-5">
                <slot name="content"></slot>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
    import JetSectionTitle from './SectionTitle.vue'

    export default {
        components: {
            JetSectionTitle,
        }
    }
</script>
