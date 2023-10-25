<template>
    <button :type="type" class="btn btn-secondary">
        <slot></slot>
    </button>
</template>

<script lang="jsx">
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    }
</script>
