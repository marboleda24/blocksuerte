<template>
    <label>
        <span v-if="value">{{ value }}</span>
        <span v-else><slot></slot></span>
    </label>
</template>

<script lang="jsx">
    export default {
        props: ['value']
    }
</script>
