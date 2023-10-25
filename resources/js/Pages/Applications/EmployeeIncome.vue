<template>
    <div>
        <Head title="Ingreso de personal y invitados"/>

        <div class="box h-screen">
            <div class="p-10">
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-4 py-12">
                    <div class="text-center pb-12">
                        <img src="/dist/images/ev_logo/ev_logo.png" alt="" class="mx-auto w-56">
                        <h2 class="text-base font-bold mt-4">
                            Ingreso por lectura de cedula o ingresando su numero de documento
                        </h2>
                        <h1 class="font-bold text-3xl md:text-4xl lg:text-5xl font-heading">
                            Ingreso de persona e invitados
                        </h1>

                        <h2 class="text-base text-2xl md:text-3xl lg:text-4xl font-bold mt-8">
                            {{ timestamp }}
                        </h2>
                    </div>

                    <div class="mt-3">
                        <h2 class="text-center text-base font-bold">
                            Documento de identidad
                        </h2>
                        <input type="text" class="form-control form-control-lg"
                               placeholder="documento de identidad"
                               ref="document"
                               v-model="original_string"
                               @keypress.enter="checkDocument()" autofocus>
                    </div>

                    <div class="mt-10" v-if="result">
                        <h2 class="text-center text-base text-2xl md:text-3xl lg:text-4xl font-bold" :class="{'text-success': result?.state, 'text-danger': !result?.state}">
                            {{ result.msg }}
                        </h2>
                    </div>
                </section>

            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import Empty from "@/Layouts/Empty.vue";
import {Head} from "@inertiajs/vue3";

export default {
    layout: Empty,

    components: {
        Empty,
        Head
    },

    data(){
        return {
            original_string: '',
            timestamp: '',
            result: {},
        }
    },

    created() {
        setInterval(this.getNow, 1000);
    },

    mounted() {
        this.$refs.document.focus();
    },

    methods: {
        checkDocument(){
            if (!this.original_string || this.original_string.trim() === ''){
                this.$swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'El documento de identidad es obligatorio',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            }else {
                if (this.original_string.includes(';')){
                    const values = this.original_string.split(';')
                    this.checkGuest(values[0])
                }else {
                    this.checkGuest(this.original_string)
                }
            }
        },

        checkGuest(document){
            axios.get(route('employee-income.check-guest', document)).then(resp => {
                if (resp.data.state){
                    this.result = resp.data
                    this.original_string = '';
                }else if (!resp.data.state && this.original_string.includes(';')){
                    this.registerGuest(this.original_string)
                }else {
                    this.result = {
                        msg: 'Invitado no registrado, por favor lea su documento con el lector para registrarse',
                        state: false
                    }
                    this.original_string = ''
                }
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        registerGuest(original_string){
            axios.post(route('employee-income.register-guest'), {
                original_string: original_string
            }).then(resp => {
                this.result = resp.data
                this.original_string = ''
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        getNow() {
            const today = new Date();

            this.timestamp  = this.$h.formatDate(today, 'dddd, MMMM D, YYYY hh:mm:ss A')
        }
    }


}
</script>
