<template>
    <div>
        <Head title="Edicion de facturas"/>

        <portal to="application-title">
            Edicion de facturas
        </portal>

        <div class="intro-y box" v-if="search.open">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">Busqueda de facturas</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-5">
                    <input type="text" class="form-control col-span-6" v-model="search.invoice">
                    <button class="btn btn-primary col-span-6" @click="getInvoice">Consultar Documento</button>
                </div>
            </div>
        </div>


        <div class="intro-y box overflow-hidden" v-else>
            <div class="flex flex-col lg:flex-row pt-10 px-5 sm:px-20 sm:pt-20 lg:pb-20 text-center sm:text-left">
                <div class="font-semibold text-primary text-3xl">FACTURA: {{ invoice.NUMERO }}</div>
                <div class="mt-20 lg:mt-0 lg:ml-auto lg:text-right">
                    <div class="text-xl text-primary font-medium">{{ invoice.RAZONSOCIAL }}</div>
                    <div class="mt-1">{{ invoice.IDENTIFICACION }}</div>
                    <div class="mt-1">{{ invoice.CORREOFE }}</div>
                </div>
            </div>
            <div
                class="flex flex-col lg:flex-row border-b px-5 sm:px-20 pt-10 pb-10 sm:pb-20 text-center sm:text-left">
                <div>
                    <div class="text-base text-slate-500">Detalles del cliente</div>
                    <div class="text-lg font-medium text-primary mt-2">{{ invoice.RAZONSOCIAL }}</div>
                    <div class="mt-1">{{ invoice.DESCPLAZO }}</div>
                    <div class="mt-1">{{ `${invoice.CODVENDEDOR} – ${invoice.NOMVENDEDOR}` }}</div>
                </div>
                <div class="mt-10 lg:mt-0 lg:ml-auto lg:text-right">
                    <div class="text-base text-slate-500">FECHA</div>
                    <div class="text-lg text-primary font-medium mt-2">{{ invoice.FECHA }}</div>
                    <div class="mt-1">{{ invoice.VENCIMIENTO }}</div>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-5 sm:py-10">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">DESCRIPCION</th>
                            <th class="text-right whitespace-nowrap">CANTIDAD</th>
                            <th class="text-right whitespace-nowrap">PRECIO</th>
                            <th class="text-right whitespace-nowrap">SUBTOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in invoice.details">
                            <td class="">
                                <div class="font-medium whitespace-nowrap">{{ item.CodigoProducto }}</div>
                                <div class="text-slate-500 text-sm mt-0.5 whitespace-nowrap">
                                    {{ item.DescripcionProducto }}
                                </div>
                            </td>
                            <td class="text-right w-32">{{ parseFloat(item.Cantidad).toFixed(2) }}</td>
                            <td class="text-right w-32">
                                <currency-input v-model="item.Precio"
                                                class="form-control-sm"
                                                :options="{
                                                        currency: 'COP',
                                                        locale: 'es-CO'
                                                    }"
                                />
                            </td>
                            <td class="text-right w-32 font-medium">
                                {{ $h.formatCurrency(item.Precio * item.Cantidad) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-5 sm:px-20 pb-10 sm:pb-20 flex flex-col-reverse sm:flex-row">
                <div class="text-center sm:text-right sm:ml-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="whitespace-nowrap">BRUTO</th>
                            <th class="whitespace-nowrap text-right w-32">{{ $h.formatCurrency(bruto) }}</th>
                        </tr>

                        <tr>
                            <th class="whitespace-nowrap">DESCUENTO</th>
                            <th class="whitespace-nowrap text-right w-32">
                                <currency-input v-model="invoice.DESCUENTO"
                                                class="form-control-sm"
                                                :options="{
                                                        currency: 'COP',
                                                        locale: 'es-CO'
                                                    }"
                                />
                            </th>
                        </tr>

                        <tr>
                            <th class="whitespace-nowrap">SUBTOTAL</th>
                            <th class="whitespace-nowrap text-right w-32">
                                {{ $h.formatCurrency(parseFloat(bruto) - parseFloat(invoice.DESCUENTO)) }}
                            </th>
                        </tr>

                        <tr>
                            <th class="whitespace-nowrap">
                                <div class="items-center justify-items-center">
                                    <input type="checkbox" class="form-check-input mr-1" v-model="invoice.taxable">
                                    IVA
                                </div>
                            </th>
                            <th class="whitespace-nowrap text-right w-32">{{ $h.formatCurrency(taxes) }}</th>
                        </tr>

                        <tr>
                            <th class="whitespace-nowrap">TOTAL</th>
                            <th class="whitespace-nowrap text-right w-32">{{
                                    $h.formatCurrency((parseFloat(bruto) - parseFloat(invoice.DESCUENTO)) + parseFloat(taxes))
                                }}
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center p-5 border-t border-slate-200/60">
                <div class="form-check form-switch w-full sm:w-auto sm:mr-auto mt-3 sm:mt-0">
                    <button class="btn btn-primary mr-2" @click="update">Guardar</button>
                    <button class="btn btn-secondary" @click="reset">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import CurrencyInput from "@/Components/CurrencyInput.vue";
import {Head, Link} from "@inertiajs/vue3";

export default {
    components: {
        CurrencyInput,
        Head,
        Link
    },

    data() {
        return {
            invoice: {},
            search: {
                invoice: '',
                open: true
            }
        }
    },

    methods: {
        getInvoice() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Procesando solicitud…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('invoice-edition.search-invoice'), {
                params: {
                    invoice: this.search.invoice
                }
            }).then(resp => {
                this.invoice = resp.data
                this.search = {
                    invoice: '',
                    open: false
                }
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: `Ups: ${err.response.status}`,
                    text: err.response.data,
                    confirmButtonText: 'Aceptar'
                });

                this.search = {
                    invoice: '',
                    open: true
                }
            })
        },

        update() {
            this.$swal({
                icon: 'question',
                title: '¿Guardar cambios?',
                text: "Por favor, recuerde revisar bien la información antes de continuar",
                showCancelButton: true,
                confirmButtonText: '¡Si, Guardar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$swal({
                        iconHtml: this.$h.loadIcon(),
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Procesando solicitud…',
                        text: 'Este proceso puede tomar unos segundos, espere por favor…',
                    });

                    axios.post(route('invoice-edition.update'), this.invoice).then(resp => {
                        this.search = {
                            invoice: '',
                            open: true
                        }
                        this.invoice = {}

                        this.$swal({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Documento actualizado con exito',
                            confirmButtonText: 'Aceptar'
                        });
                    }).catch(err => {
                        this.$swal({
                            icon: 'error',
                            title: `Ups: ${err.response.status}`,
                            text: err.response.data,
                            confirmButtonText: 'Aceptar'
                        });
                    })
                }
            })
        },

        reset() {
            this.$swal({
                icon: 'question',
                title: '¿Cancelar edición?',
                text: "Todos los cambios realizados se descartaran",
                showCancelButton: true,
                confirmButtonText: '¡Si, continuar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.search = {
                        invoice: '',
                        open: true
                    }
                    this.invoice = {}
                }
            })
        }
    },

    computed: {
        bruto() {
            let value = this.invoice.details.reduce(function (a, c) {
                return a + Number((c.Precio * c.Cantidad) || 0)
            }, 0)

            this.invoice.BRUTO = value
            return value;
        },

        taxes() {
            let value = this.invoice.taxable
                ? (this.bruto - this.invoice.DESCUENTO) * 0.19
                : 0

            this.invoice.IVA = value
            return value;
        }
    }
}
</script>
