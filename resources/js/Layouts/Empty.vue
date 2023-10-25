<template>
    <div>
        <DarkModeSwitcher />
        <MainColorSwitcher/>
        <LoadingSpinner/>

        <slot></slot>
    </div>
</template>
<script lang="jsx">
    import DarkModeSwitcher from "@/Components/DarkModeSwitcher.vue";
    import MainColorSwitcher from "@/Components/MainColorSwitcher.vue";
    import LoadingSpinner from "@/GlobalComponents/LoadingSpinner.vue";

    export default {
        components: {
            DarkModeSwitcher,
            MainColorSwitcher,
            LoadingSpinner
        }
    }
</script>
