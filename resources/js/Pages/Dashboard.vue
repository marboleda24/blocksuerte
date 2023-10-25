<template>
    <div>
        <Head title="Dashboard" />

        <portal to="application-title">
            Dashboard
        </portal>

        <welcome  />
    </div>
</template>

<script lang="jsx">
    import Welcome from '@/Jetstream/Welcome.vue'
    import {Head} from "@inertiajs/vue3";

    import dayjs from "dayjs";
    import 'dayjs/locale/es'
    dayjs.locale('es')

    export default {
        components: {
            Welcome,
            Head
        },
    }
</script>
