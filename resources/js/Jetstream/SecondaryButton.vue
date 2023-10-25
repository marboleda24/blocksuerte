<template>

    <button :type="type" class="btn btn-primary">
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
