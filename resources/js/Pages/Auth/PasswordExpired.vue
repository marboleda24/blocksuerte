<template>
    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <!-- BEGIN: Login Info -->
            <div class="hidden xl:flex flex-col min-h-screen">
                <div class="-intro-x flex items-center">
                    <img alt="evpiu" class="w-40" src="/img/evpiu_v2.png" draggable="false">
                </div>
                <div class="my-auto">
                    <img alt="evpiu" class="-intro-x w-2/3 mt-14" src="/img/login_img.png" draggable="false">
                    <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">
                        Tu contraseña ha expirado
                        <br>
                        Cambia tu contraseña para continuar...
                    </div>
                    <div class="-intro-x mt-5 text-lg text-white">Plataforma de información unificada de Estrada Velasquez</div>
                </div>
            </div>
            <!-- END: Login Info -->
            <!-- BEGIN: Login Form -->
            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <div class="my-auto mx-auto xl:ml-50 bg-white xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Tu contraseña ha expirado</h2>
                    <div class="intro-x mt-2 text-gray-500 xl:hidden text-center">Cambia tu contraseña para continuar...</div>
                    <form @submit.prevent="submit">
                        <div class="intro-x mt-8">
                            <jet-validation-errors class="mb-4" />
                            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                                {{ status }}
                            </div>
                            <jet-input type="password" class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4" v-model="form.current_password" placeholder="Contraseña Actual" required />
                            <jet-input type="password" class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4" v-model="form.password" placeholder="Nueva Contraseña" required />
                            <jet-input type="password" class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4" v-model="form.password_confirmation" placeholder="Confirmar Contraseña" required />
                        </div>
                        <div class="intro-x mt-5 xl:mt-8 xl:flex justify-center xl:justify-start">
                            <button class="btn btn-primary py-3 px-4 w-full align-top" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <font-awesome-icon icon="user-lock" class="mr-2"/>
                                Cambiar contraseña
                            </button>
                        </div>
                        <div class="intro-x mt-10 xl:mt-10 text-gray-700 text-center xl:text-left">
                            Al usar nuestra plataforma, usted acepta nuestros <br> <a class="text-theme-1" href="">Términos y Condiciones</a> & <a class="text-theme-1" href="">Politica de Privacidad</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END: Login Form -->
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
import {defineComponent} from "vue";


export default defineComponent({
    components: {
        JetButton,
        JetInput,
        JetCheckbox,
        JetValidationErrors,
        Empty
    },

    layout: Empty,

    metaInfo: {
        title: 'Tu Contraseña Expiro',
    },

    props: {
        username: String,
        status: String
    },

    data() {
        return {
            form: this.$inertia.form({
                username: '',
                current_password: '',
                password: '',
                password_confirmation: ''
            })
        }
    },


    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ... data,
                }))
                .post(route('password.post_expired'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
        }
    },

    mounted() {
        dom("body").removeClass("app").addClass("login");
    }
})
</script>
