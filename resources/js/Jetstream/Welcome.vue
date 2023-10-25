<template>
    <div>
        <jet-application-logo class="block h-12 w-12" />

        <div class="mt-8 text-2xl">
            Bienvenido a EVPIU
        </div>

        <div class="mt-6 text-gray-500">
            <i data-feather="activity" class="mx-auto"></i>
            Plataforma de información unificada de Estrada Velasquez
        </div>
    </div>
</template>

<script lang="jsx">
    import JetApplicationLogo from '@/Jetstream/ApplicationLogo.vue'

    export default {
        components: {
            JetApplicationLogo,
        },
    }
</script>
