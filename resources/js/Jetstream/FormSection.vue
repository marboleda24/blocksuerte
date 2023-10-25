<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <jet-section-title>
            <template #title><slot name="title"></slot></template>
            <template #description><slot name="description"></slot></template>
        </jet-section-title>


        <div class="intro-y box mt-5 md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div class="p-5">
                    <div class="grid grid-cols-4 gap-6">
                         <slot name="form"></slot>
                    </div>
                </div>
                <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6" v-if="hasActions">
                    <slot name="actions"></slot>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="jsx">
    import JetSectionTitle from './SectionTitle.vue'

    export default {
        components: {
            JetSectionTitle,
        },

        computed: {
            hasActions() {
                return !! this.$slots.actions
            }
        }
    }
</script>
