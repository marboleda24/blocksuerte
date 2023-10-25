<template>
    <div>
        <Head title="Facturación por vendedor"/>

        <portal to="application-title">
            Facturación por vendedor
        </portal>

        <portal to="actions">
            <div class="flex flex-row">
                <Litepicker
                    v-model="form.date_range"
                    :options="{
                    autoApply: false,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    format: 'DD-MM-YYYY',
                    lang: 'es-ES',
                    dropdowns: {
                        minYear: 2021,
                        maxYear: null,
                        months: true,
                        years: true
                    },
                    maxDate: current_date
                }"
                    class="form-control w-80"
                    @button:apply="search"
                />

                <select class="form-select ml-2" v-model="orderBy">
                    <option value="invoice">Ordenar por factura</option>
                    <option value="customer">Consolidar por cliente</option>
                </select>

                <select class="form-select ml-2"
                        v-model="form.seller"
                        v-permission:has.disabled="'queries.invoices-per-seller.show-all'">
                    <option value="" selected disabled>Seleccione…</option>
                    <option v-for="seller in sellers" :value="seller.vendor_code">
                        {{ `${seller.vendor_code} - ${seller.name}` }}
                    </option>
                </select>
            </div>

        </portal>

        <div>
            <div class="box p-2 mb-24">
                <div class="overflow-y-auto">
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr class="text-center uppercase">
                            <th>TIPO</th>
                            <th>NUMERO</th>
                            <th>FECHA</th>
                            <th>OV</th>
                            <th>OC</th>
                            <th>RAZON SOCIAL</th>
                            <th>BRUTO</th>
                            <th>DESCUENTO</th>
                            <th>SUBTOTAL</th>
                        </tr>
                        </thead>
                        <tbody v-if="data.length > 0">
                        <tr v-for="row in data" :class="{'text-danger': row.TIPODOC !== 'CU'}">
                            <td class="text-center">
                                {{ row.TIPODOC === 'CU' ? 'FACTURA' : 'NOTA CREDITO' }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-secondary btn-sm"
                                        @click="show(row.NUMERO)" :disabled="row.TIPODOC !== 'CU'">
                                    {{ row.NUMERO }}
                                </button>
                            </td>
                            <td class="text-center">{{ row.FECHA }}</td>
                            <td class="text-center">{{ row.OV }}</td>
                            <td class="text-center">{{ row.OC.length > 0 ? row.OC : '-' }}</td>
                            <td class="text-left">{{ row.RAZONSOCIAL }}</td>
                            <td class="text-right">{{(row.TIPODOC !== 'CU' ? '-': '') + $h.formatCurrency(row.BRUTO) }}</td>
                            <td class="text-right">{{(row.TIPODOC !== 'CU' ? '-': '') + $h.formatCurrency(row.DESCUENTO) }}</td>
                            <td class="text-right">{{(row.TIPODOC !== 'CU' ? '-': '') + formatter(parseFloat(row.BRUTO) - parseFloat(row.DESCUENTO)) }}</td>
                        </tr>
                        <tr class="font-bold text-base">
                            <td class="text-right" colspan="6">TOTALES</td>
                            <td class="text-right">{{ $h.formatCurrency(bruto) }}</td>
                            <td class="text-right">{{ $h.formatCurrency(discount) }}</td>
                            <td class="text-right">{{ $h.formatCurrency(total) }}</td>
                        </tr>
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="12" class="text-center text-danger">
                                No se encontraron documentos o no se ha realizado ninguna búsqueda
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <jet-dialog-modal :show="modal.open" max-width=5xl>
                <template #title v-if="modal.data">
                    Factura # {{ modal.data.header.NUMERO }}
                </template>

                <template #content>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="flex flex-row">
                            <strong>CLIENTE:</strong> {{ modal.data.header.RAZONSOCIAL }}
                        </div>
                        <div class="flex flex-row">
                            <strong>NIT/CC:</strong> {{ modal.data.header.IDENTIFICACION }}
                        </div>
                        <div class="flex flex-row">
                            <strong>FECHA:</strong> {{ $h.formatDate(modal.data.header.FECHA) }}
                        </div>
                        <div class="flex flex-row">
                            <strong>VENCIMIENTO:</strong> {{ $h.formatDate(modal.data.header.VENCIMIENTO) }}
                        </div>
                        <div class="flex flex-row">
                            <strong>VENDEDOR:</strong> {{ modal.data.header.NOMVENDEDOR }}
                        </div>
                    </div>

                    <table class="table table-sm table-bordered mt-5">
                        <thead>
                        <tr class="text-center">
                            <th>CÓDIGO</th>
                            <th>DESCRIPCIÓN</th>
                            <th>NOTAS</th>
                            <th>ARTE</th>
                            <th>MARCA</th>
                            <th>PRECIO</th>
                            <th>CANTIDAD</th>
                            <th>TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in modal.data.detail">
                            <td class="text-center">{{ item.CodigoProducto }}</td>
                            <td>{{ item.DescripcionProducto }}</td>
                            <td>
                                {{ item.notes.join('\n') }}
                            </td>
                            <td class="text-center">{{ item.ARTE }}</td>
                            <td class="text-center">{{ item.Marca }}</td>
                            <td class="text-right">{{ $h.formatCurrency(item.Precio) }}</td>
                            <td class="text-right">{{ parseInt(item.Cantidad) }}</td>
                            <td class="text-right">{{ $h.formatCurrency(item.ValorMercancia) }}</td>
                        </tr>
                        </tbody>
                    </table>


                    <table class="table table-sm table-bordered mt-5">
                        <thead>
                            <tr class="text-center">
                                <th>BRUTO</th>
                                <th>DESCUENTO</th>
                                <th>RTE. FUENTE</th>
                                <th>RTE. IVA</th>
                                <th>SUBTOTAL</th>
                                <th>IVA</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center">
                            <td>{{ $h.formatCurrency(modal.data.header.BRUTO, 0) }}</td>
                            <td>{{ $h.formatCurrency(modal.data.header.DESCUENTO, 0) }}</td>
                            <td>{{ $h.formatCurrency(modal.data.header.RTEFTE, 0) }}</td>
                            <td>{{ $h.formatCurrency(modal.data.header.RTEIVA, 0) }}</td>
                            <td>{{ $h.formatCurrency(modal.data.header.SUBTOTAL, 0) }}</td>
                            <td>{{ $h.formatCurrency(modal.data.header.IVA, 0) }}</td>
                            <td>
                                {{ $h.formatCurrency((parseFloat(modal.data.header.BRUTO) + parseFloat(modal.data.header.IVA)) - (parseFloat(modal.data.header.DESCUENTO) + parseFloat(modal.data.header.RTEFTE) + parseFloat(modal.data.header.RTEIVA)), 0) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </template>

                <template #footer>
                    <button @click="closeModal" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";

export default {
    props: {
        sellers: Array
    },

    components: {
        Head,
        JetDialogModal
    },

    data() {
        return {
            data: [],
            form: {
                date_range: '',
                seller: this.$page.props.user.vendor_code ?? ''
            },
            modal: {
                data: {},
                open: false
            },
            count: 0,
            orderBy: 'invoice'
        }
    },

    methods: {
        search(startDate, endDate) {
            if (this.form.seller === ''){
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Vendedor no valido o no seleccionado.',
                    toast: true,
                    position: 'bottom-start',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    reverseButtons: false,
                });
            }else {
                this.$swal({
                    iconHtml: this.$h.loadIcon(),
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Cargando información…',
                    text: 'Este proceso puede tardar unos segundos.',
                });

                axios.get(route('queries.invoices-per-seller.search'), {
                    params: {
                        start_date: startDate,
                        end_date: endDate,
                        seller: this.form.seller,
                        orderBy: this.orderBy
                    }
                }).then(resp => {
                    this.data = resp.data

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
                        text: 'Hubo un error recuperando la información.',
                        confirmButtonText: 'Aceptar',
                    });
                    console.log(err)
                })
            }
        },

        formatter(number) {
            if (typeof number !== 'number') {
                return 0
            } else {
                return this.$h.formatCurrency(number)
            }
        },

        show(invoice) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('queries.invoices-per-seller.show-invoice', invoice)).then(resp => {
                this.modal = {
                    data: resp.data,
                    open: true
                }
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err);
            })
        },

        closeModal(){
            this.modal = {
                data: {},
                open: false
            }
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },

        date_range() {
            return this.form.date_range
        },

        bruto() {
            return this.data.reduce(function (a, c) {
                return a + Number(c.TIPODOC === 'CU' ? c.BRUTO : -Math.abs(c.BRUTO) || 0)
            }, 0)
        },
        discount() {
            return this.data.reduce(function (a, c) {
                return a + Number(c.TIPODOC === 'CU' ? c.DESCUENTO : -Math.abs(c.DESCUENTO) || 0)
            }, 0)
        },

        total() {
            return (this.bruto - this.discount)
        }
    },
}
</script>
