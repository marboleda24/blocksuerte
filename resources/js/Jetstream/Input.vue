<template>
    <input class="form-control" :value="value" @input="$emit('input', $event.target.value)" ref="input">
</template>

<script lang="jsx">
    export default {
        props: ['value'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    }
</script>

