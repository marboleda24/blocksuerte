<template>
    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <!-- BEGIN: Login Info -->
            <div class="hidden xl:flex flex-col min-h-screen">
                <div class="-intro-x flex items-center pt-5">
                    <img alt="evpiu" class="w-40" src="img/evpiu_v2.png" draggable="false">
                </div>
                <div class="my-auto">
                    <img alt="evpiu" class="-intro-x w-2/3 mt-14" src="img/two-factor-img.png" draggable="false">
                    <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">
                        2FA
                        <br>
                        Verificacion de identidad
                    </div>
                    <div class="-intro-x mt-5 text-lg text-white">Plataforma de información unificada de Estrada Velasquez</div>
                </div>
            </div>
            <!-- END: Login Info -->

            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <div class="my-auto mx-auto xl:ml-50 bg-white xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Verifica tu identidad</h2>
                    <div class="intro-x mt-2 text-gray-500 xl:hidden text-center">Por favor, verifica tu identidad para continuar</div>
                    <form @submit.prevent="submit">
                        <div class="intro-x mt-8">
                            <jet-validation-errors class="mb-4" />

                            <div v-if="! recovery">
                                <jet-input ref="code" id="code" type="text" inputmode="numeric" class="intro-x login__input input input--lg border border-gray-300 block" v-model="form.code" placeholder="Código" autofocus autocomplete="one-time-code" />
                            </div>
                            <div v-else>
                                <jet-input ref="recovery_code" id="recovery_code" type="text" class="intro-x login__input input input--lg border border-gray-300 block" v-model="form.recovery_code" placeholder="Código de recuperacion" autocomplete="one-time-code" />
                            </div>

                        </div>
                        <div class="intro-x flex text-gray-700 text-xs sm:text-sm mt-4">
                            <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer" @click.prevent="toggleRecovery">
                                <template v-if="! recovery">
                                    Usar código de recuperacion
                                </template>

                                <template v-else>
                                    Usar código de autenticacion
                                </template>
                            </button>
                        </div>
                        <div class="intro-x mt-5 xl:mt-8 xl:flex justify-center xl:justify-start">
                            <button class="button button--lg w-full text-white bg-theme-1 xl:mr-0" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Continuar
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
    import Empty from '@/Layouts/Empty.vue'
    import {defineComponent} from "vue";


    export default defineComponent({
        components: {
            JetAuthenticationCard,
            JetAuthenticationCardLogo,
            JetButton,
            JetInput,
            JetLabel,
            JetValidationErrors,
            Empty
        },

        layout: Empty,

        metaInfo: {
            title: 'Verificacion de identidad',
        },

        data() {
            return {
                recovery: false,
                form: this.$inertia.form({
                    code: '',
                    recovery_code: '',
                })
            }
        },

        methods: {
            toggleRecovery() {
                this.recovery ^= true

                this.$nextTick(() => {
                    if (this.recovery) {
                        this.$refs.recovery_code.focus()
                        this.form.code = '';
                    } else {
                        this.$refs.code.focus()
                        this.form.recovery_code = ''
                    }
                })
            },

            submit() {
                this.form.post(route('two-factor.login'))
            }
        },
        mounted() {
            ("body").removeClass("app").addClass("login");
        }
    })
</script>
