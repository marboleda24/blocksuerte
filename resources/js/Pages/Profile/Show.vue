<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Perfil
            </h2>
        </portal>

        <div>
            <div class="max-w-8xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-profile-information-form :user="$page.props.user" />

                <jet-section-border />

                <update-password-form class="mt-10 sm:mt-0" />

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <jet-section-border />

                    <two-factor-authentication-form class="mt-10 sm:mt-0" />
                </div>

                <jet-section-border />

                <logout-other-browser-sessions-form :sessions="sessions" class="mt-10 sm:mt-0" />

                <jet-section-border />

                <delete-user-form class="mt-10 sm:mt-0" />
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
    import DeleteUserForm from './DeleteUserForm.vue'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
    import LogoutOtherBrowserSessionsForm from './LogoutOtherBrowserSessionsForm.vue'
    import TwoFactorAuthenticationForm from './TwoFactorAuthenticationForm.vue'
    import UpdatePasswordForm from './UpdatePasswordForm.vue'
    import UpdateProfileInformationForm from './UpdateProfileInformationForm.vue'
    import {defineComponent} from "vue";

    export default defineComponent({
        metaInfo: { title: 'Perfil' },

        props: ['sessions'],

        components: {
            DeleteUserForm,
            JetSectionBorder,
            LogoutOtherBrowserSessionsForm,
            TwoFactorAuthenticationForm,
            UpdatePasswordForm,
            UpdateProfileInformationForm,
        },
    })
</script>
