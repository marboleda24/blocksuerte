<template>
    <div>
        <Head title="Iniciar sesion"/>

        <div class="container sm:px-10">
            <div class="block xl:grid grid-cols-2 gap-4">
                <!-- BEGIN: Login Info -->
                <div class="hidden xl:flex flex-col min-h-screen">
                    <div class="my-auto">
                        <img alt="evpiu" class="-intro-x w-2/3 mt-14" src="img/login_img.png" draggable="false">
                        <div class="-intro-x text-white font-medium text-3xl leading-tight mt-10">
                            ¡Bienvenido!
                            <br>
                            Inicia sesión para continuar
                        </div>
                        <div class="-intro-x mt-5 text-lg text-white">Plataforma de información unificada de Estrada
                            Velasquez
                        </div>
                    </div>
                </div>
                <!-- END: Login Info -->
                <!-- BEGIN: Login Form -->
                <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                    <div
                        class="my-auto mx-auto xl:ml-50 bg-white xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                        <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Iniciar sesión</h2>
                        <div class="intro-x mt-2 text-gray-500 xl:hidden text-center">Por favor, inicia sesión para
                            continuar
                        </div>
                        <form @submit.prevent="submit">
                            <div class="intro-x mt-8">
                                <jet-validation-errors class="mb-4"/>
                                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                                    {{ status }}
                                </div>
                                <input type="text"
                                       class="intro-x login__input form-control py-3 px-4 border-gray-300 block"
                                       v-model="form.username" placeholder="Usuario" required autofocus/>
                                <input type="password"
                                       class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4"
                                       v-model="form.password" placeholder="Contraseña" required
                                       autocomplete="current-password"/>
                            </div>
                            <div class="intro-x flex text-gray-700 text-xs sm:text-sm mt-4">
                                <div class="flex items-center mr-auto">
                                    <input type="checkbox" class="form-check-input" name="remember"
                                           v-model="form.remember"/>
                                    <span class="ml-2 text-sm text-gray-600">Recuérdame</span>
                                </div>
                                <Link v-if="canResetPassword" :href="route('password.request')" class="text-theme-1">
                                    ¿Olvidaste tu contraseña?
                                </Link>
                            </div>
                            <div class="intro-x mt-5 xl:mt-8 xl:flex justify-center xl:justify-start">
                                <button class="btn btn-primary py-3 px-4 w-full align-top"
                                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <font-awesome-icon :icon="['fas', 'sign-in-alt']" class="mr-2"/>
                                    Iniciar sesión
                                </button>
                            </div>
                            <div class="intro-x mt-10 xl:mt-24 text-gray-700 text-center xl:text-left">
                                Al iniciar sesión, usted acepta nuestros <br> <a class="text-theme-1" href="">Terminos y
                                Condiciones</a> & <a class="text-theme-1" href="">Politica de Privacidad</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END: Login Form -->
            </div>
        </div>
    </div>

</template>

<script lang="jsx">
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import Empty from '@/Layouts/Empty.vue'
import dom from "@left4code/tw-starter/dist/js/dom";
import {Head, Link, router} from '@inertiajs/vue3';
import {reactive} from 'vue'

export default {
    layout: Empty,

    components: {
        JetButton,
        JetInput,
        JetCheckbox,
        JetValidationErrors,
        Empty,
        Head,
        Link
    },

    props: {
        canResetPassword: Boolean,
        status: String
    },

    setup() {
        const form = reactive({
            username: '',
            password: '',
            remember: false,
        })

        function submit() {
            router.post(route('login'), form)
        }

        return {form, submit}
    },

    mounted() {
        dom("body").removeClass("main").removeClass("error-page").addClass("login");
    }

}
</script>
