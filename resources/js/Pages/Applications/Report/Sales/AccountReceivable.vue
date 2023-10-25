<template>
    <div>
        <Head title="Análisis de cuentas por cobrar detallado"/>

        <portal to="application-title">
            Análisis de cuentas por cobrar detallado
        </portal>

        <div class="box">
            <div class="p-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label>Vendedor(a)</label>

                        <TomSelect v-model="form.seller"
                                   class="w-full"
                                   :class="{ 'border-danger': v$.form.seller.$error }"
                                   v-permission:has.disabled="'queries.account-receivable.show-all'">
                            <option value="">Seleccione…</option>
                            <option v-for="seller in sellers" :value="seller.vendor_code">{{ seller.name }}</option>
                        </TomSelect>

                        <template v-if="v$.form.seller.$error">
                            <ul class="mt-1">
                                <li class="text-danger" v-for="(error, index) of v$.form.seller.$errors" :key="index">
                                    {{ error.$message }}
                                </li>
                            </ul>
                        </template>
                    </div>

                    <button class="btn btn-primary" @click="download">
                        <font-awesome-icon icon="download"  class="mr-1"/>
                        Descargar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    props: {
        sellers: Array
    },

    components: {
        Head
    },

    validations(){
        return {
            form: {
                seller: {
                    required
                }
            }
        }
    },

    data(){
        return {
            form: {
                seller: ''
            }
        }
    },

    methods: {
        download(){
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
                    text: 'Este proceso puede tomar unos segundos, espere por favor…',
                });

                axios.post(route('account-receivable.download'), this.form, {
                    responseType: 'blob'
                }).then(resp => {
                    const url = window.URL.createObjectURL(new Blob([resp.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'análisis-cuentas-por-cobrar-detallado.pdf');

                    document.body.appendChild(link);
                    link.click();

                    this.$swal.close();
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data.message,
                        timerProgressBar: true,
                        showConfirmButton: true,
                    });
                })
            }
        }
    }
}
</script>
