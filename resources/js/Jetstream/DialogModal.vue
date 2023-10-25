<template>
    <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="flex flex-row justify-start px-6 py-4 text-left text-lg border-b">
            <slot name="title">
            </slot>
        </div>

        <div class="px-6 py-4">
            <slot name="content">
            </slot>
        </div>

        <div class="flex flex-row justify-end px-6 py-4 text-right border-t">
            <slot name="footer">
            </slot>
        </div>
    </modal>
</template>

<script lang="jsx">
import { defineComponent } from 'vue'
import Modal from './Modal.vue'

export default defineComponent({
    emits: ['close'],

    components: {
        Modal,
    },

    props: {
        show: {
            default: false
        },
        maxWidth: {
            default: '2xl'
        },
        closeable: {
            default: true
        },
    },

    methods: {
        close() {
            this.$emit('close')
        },
    }
})
</script>
