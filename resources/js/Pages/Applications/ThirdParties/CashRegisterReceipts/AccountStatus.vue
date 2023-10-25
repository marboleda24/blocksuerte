<template>
    <div>
        <Head title="Estado de cuenta"/>

        <portal to="application-title">
            Estado de cuenta
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <autocomplete
                            :url="route('cash-register-receipts.search-customer')"
                            show-field="name"
                            @selected-value="customerData"
                            class="w-full"
                        />

                        <template v-if="v$.form.customer_code.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.customer_code.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <button @click="downloadReport"
                            class="btn btn-primary">
                        <font-awesome-icon icon="download" class="mr-2"/>
                        Descargar Estado de cuenta
                    </button>
                </div>
            </div>

        </div>

    </div>
</template>

<script lang="jsx">
import Autocomplete from "@/GlobalComponents/Autocomplete/Main.vue";
import {Head, Link} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        JetDialogModal,
        Head,
        Link,
        Autocomplete
    },

    validations() {
        return {
            form: {
                customer_code: {
                    required
                }
            }
        }
    },

    data(){
        return {
            form: {
                customer_code: ''
            }
        }
    },

    methods: {
        customerData(obj){
            this.form.customer_code = obj.code
        },

        downloadReport(){
            this.v$.form.$touch();

            if (this.v$.form.$invalid) {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Verifica que toda la información este correctamente diligenciada.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.post(route('cash-register-receipts.account-status-pdf'), this.form, {
                    responseType: 'blob'
                }).then(resp => {
                    const url = window.URL.createObjectURL(new Blob([resp.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'estado-cuenta.pdf');

                    document.body.appendChild(link);
                    link.click();

                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data.message,
                        confirmButtonText: 'Aceptar'
                    });
                })
            }
        }
    }
}
</script>
