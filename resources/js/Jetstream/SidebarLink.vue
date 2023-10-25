<template>
    <inertia-link :href="href" :class="classes">
        <slot></slot>
    </inertia-link>
</template>

<script lang="jsx">
export default {
    props: ['href', 'active'],

    computed: {
        classes() {
            return this.active
                ? 'side-menu side-menu--active'
                : 'side-menu'
        }
    }
}
</script>

<style scoped>

</style>
