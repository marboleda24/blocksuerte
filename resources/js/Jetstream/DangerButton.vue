<template>
    <button :type="type" class="btn btn-danger">
        <slot></slot>
    </button>
</template>

<script lang="jsx">
    export default {
        props: {
            type: {
                type: String,
                default: 'button',
            },
        }
    }
</script>
