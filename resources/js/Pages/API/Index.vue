<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                API Tokens
            </h2>
        </portal>

        <div>
            <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">
                <api-token-manager :tokens="tokens" :available-permissions="availablePermissions" :default-permissions="defaultPermissions" />
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import ApiTokenManager from './ApiTokenManager.vue'
import JetSectionBorder from '@/Jetstream/SectionBorder.vue'

export default {
    props: [
        'tokens',
        'availablePermissions',
        'defaultPermissions',
    ],

    components: {
        ApiTokenManager,
        JetSectionBorder,
    },
}
</script>
