<template>
    <Head title="Cambio de contraseña"/>
    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <!-- BEGIN: Login Info -->
            <div class="hidden xl:flex flex-col min-h-screen">
                <div class="-intro-x flex items-center">
                    <img alt="evpiu" class="w-40" src="/img/evpiu_v2.png" draggable="false">
                </div>
                <div class="my-auto">
                    <img alt="evpiu" class="-intro-x w-2/3 mt-14" src="/img/forgot-password.png" draggable="false">
                    <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">
                        Cambio de contraseña
                    </div>
                    <div class="-intro-x mt-5 text-lg text-white">
                        Por favor, completa el formulario para cambiar tu contraseña
                    </div>
                </div>
            </div>
            <!-- END: Login Info -->
            <!-- BEGIN: Login Form -->
            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <div
                    class="my-auto mx-auto xl:ml-50 bg-white xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">
                        Cambio de contraseña
                    </h2>
                    <jet-validation-errors class="mb-4"/>
                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                        {{ status }}
                    </div>
                    <form @submit.prevent="submit">

                        <div class="intro-x mt-8">
                            <input type="email"
                                   class="intro-x login__input form-control py-3 px-4 border-gray-300 block"
                                   v-model="form.email" placeholder="Example@estradavelasquez.com" required autofocus/>

                            <input type="password"
                                   class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4"
                                   v-model="form.password" placeholder="Nueva contraseña" required autofocus/>

                            <input type="password"
                                   class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4"
                                   v-model="form.password_confirmation" placeholder="Confirmar contraseña" required
                                   autofocus/>
                        </div>

                        <div class="intro-x mt-5 xl:mt-8 xl:flex justify-center xl:justify-start">
                            <button type="submit" class="btn btn-primary py-3 px-4 w-full align-top"
                                    :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <font-awesome-icon icon="paper-plane" class="mr-2"/>
                                Cambiar contraseña
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</template>


<script lang="jsx">
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue'
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import Empty from "@/Layouts/Empty.vue";
import {reactive} from "vue";
import {Head} from "@inertiajs/vue3";
import {Inertia} from "@inertiajs/inertia";

export default {
    layout: Empty,

    components: {
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetLabel,
        JetValidationErrors,
        Head
    },

    props: {
        email: String,
        token: String,
    },

    setup(props) {
        const form = reactive({
            token: props.token,
            email: props.email,
            password: '',
            password_confirmation: '',
        })

        function submit() {
            Inertia.post(route('ldap-reset-password'), form)
        }

        return {form, submit}
    },

    mounted() {
        dom("body").removeClass("app").addClass("login");
    }
}
</script>
