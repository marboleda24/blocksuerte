<template>
    <div>
        <Head title="Ordenes de producción"/>

        <portal to="application-title">
            Ordenes de producción
        </portal>

        <div>
            <div class="max-w-6xl justify-center items-center mx-auto">
                <p class="text-2xl mx-auto">Orden de producción</p>
                <input type="number" v-model.number="form.order_number" class="form-control" :disabled="form.order">
                <button class="btn btn-primary mt-2 w-full"
                        v-if="!form.order"
                        @click="viewProductionOrder(form.order_number)">
                    Consultar
                </button>
                <button class="btn btn-warning mt-2 w-full"
                        v-else
                        @click="form.order = null">
                    Nueva consulta
                </button>
            </div>

            <div class="grid grid-cols-12 gap-5 mt-10 pt-10 border-t pb-5 border-b" v-if="form.order">
                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Orden de producción</div>
                    <div class="text-slate-500">{{ form.order.ORDNUM_10 }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Producto</div>
                    <div class="text-slate-500">{{ `${form.order.PRTNUM_10} - ${form.order.PMDES1_01}` }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Orden de venta</div>
                    <div class="text-slate-500">{{ form.order.ORDREF_10 }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Lote</div>
                    <div class="text-slate-500">{{ form.order.LOTNUM_10 }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Arte</div>
                    <div class="text-slate-500">{{ form.order.UDFREF_10 }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Cantidad actual</div>
                    <div class="text-slate-500">{{ form.order.CURQTY_10 }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Cantidad pendiente</div>
                    <div class="text-slate-500">{{ form.order.CANT_PEND }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Fecha</div>
                    <div class="text-slate-500">{{ form.order.FechaCreacion }}</div>
                </div>

                <div class="col-span-12 sm:col-span-4 2xl:col-span-3 box p-5">
                    <div class="font-medium text-base">Cantidad a registrar</div>
                    <input type="number" class="form-control form-control-sm" v-model.number="form.quantity">
                </div>
            </div>

            <button class="btn btn-primary w-full" v-if="form.order" @click="registerOrder">
                Registrar ingreso
            </button>
        </div>
    </div>
</template>

<script lang="jsx">
import useVuelidate from '@vuelidate/core'
import {required} from '@/utils/i18n-validators'
import {Head} from '@inertiajs/vue3'

export default {
    setup() {
        return {v$: useVuelidate()}
    },

    components: {
        Head
    },

    data(){
        return {
            form: {
                order_number: '',
                order: null,
                quantity: 0
            }
        }
    },

    methods: {
        viewProductionOrder(order){
            let regex = new RegExp('^[0-9]{8}$')

            if (regex.test(order)){
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Procesando solicitud…',
                    text: 'Este proceso puede tardar unos segundos.',
                });
                axios.get(route('max-update.production-orders.view', order)).then(resp => {
                    this.form.order = resp.data

                    this.$swal({
                        icon: 'success',
                        title: '¡Consulta completada!',
                        text: 'Consulta realizada con éxito.',
                        toast: true,
                        position: 'bottom-start',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        reverseButtons: false,
                    });
                }).catch(err => {
                    this.$swal({
                        icon: 'error',
                        title: '¡Ups!',
                        text: err.response.data,
                        confirmButtonText: 'Aceptar',
                    });
                })
            }else {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'La orden de producción debe de ser un valor numérico de 8 dígitos',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'Aceptar',
                });
            }
        },

        registerOrder(){
            this.$swal({
                title: 'Ingresar orden de producción',
                text: `¿Esta seguro de registrar ${this.form.quantity} unidades para la orden de producción ${this.form.order_number}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, registrar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tardar unos segundos',
                    });

                    axios.post(route('max-update.production-orders.register'), this.form).then(resp => {
                        this.$swal({
                            icon: 'success',
                            title: 'Procesado correctamente',
                            text: `La orden de producción ${this.form.order_number} ha sido ingresada correctamente`,
                            timer: 3000,
                            timerProgressBar: true,
                            confirmButtonText: 'Aceptar',
                        });

                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: '¡Ups!',
                            text: 'Hubo un error procesando la solicitud',
                            timer: 3000,
                            timerProgressBar: true,
                            confirmButtonText: 'Aceptar',
                        });
                    })
                }
            })
        }
    }
}
</script>
