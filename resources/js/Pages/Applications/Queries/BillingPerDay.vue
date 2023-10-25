<template>
    <div>
        <Head title="Facturación por dia"/>

        <portal to="application-title">
            Facturación por dia
        </portal>

        <portal to="actions">
            <Litepicker
                v-model="form.date_range"
                :options="{
                    autoApply: true,
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
            />
        </portal>


        <div>
            <v-client-table :data="table_data" :columns="columns" :options="options" ref="table"
                            class="overflow-y-auto">

                <template v-slot:actions="{row}">
                    <div class="text-center">
                        <button class="btn btn-secondary" @click="detail(row)">
                            <font-awesome-icon :icon="['far', 'eye']"/>
                        </button>
                    </div>
                </template>
            </v-client-table>

            <table class="table table-bordered table-sm mt-5 text-center mb-14">
                <thead class="table-danger">
                <tr>
                    <th class="">
                        BRUTO
                    </th>
                    <th class="">
                        DESCUENTO
                    </th>
                    <th class="">
                        RTEFTE
                    </th>
                    <th class="">
                        RTEIVA
                    </th>
                    <th class="">
                        RTEICA
                    </th>
                    <th class="">
                        SUBTOTAL
                    </th>
                    <th class="">
                        IVA
                    </th>
                    <th class="">
                        TOTAL
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="">{{ $h.formatCurrency(bruto) }}</td>
                    <td class="">{{ $h.formatCurrency(discount) }}</td>
                    <td class="">{{ $h.formatCurrency(rtefte) }}</td>
                    <td class="">{{ $h.formatCurrency(rteiva) }}</td>
                    <td class="">{{ $h.formatCurrency(rteica) }}</td>
                    <td class="">{{ $h.formatCurrency(subtotal) }}</td>
                    <td class="">{{ $h.formatCurrency(iva) }}</td>
                    <td class="">{{ $h.formatCurrency(subtotal + iva) }}</td>
                </tr>
                </tbody>
            </table>


            <jet-dialog-modal :show="isOpen" max-width=3xl>
                <template #title v-if="modal_data">
                    Factura # {{ modal_data.NUMERO }}
                </template>

                <template #content v-if="modal_data">
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="flex flex-row">
                            <strong>CLIENTE:</strong> {{ modal_data.RAZONSOCIAL }}
                        </div>
                        <div class="flex flex-row">
                            <strong>NIT/CC:</strong> {{ modal_data.IDENTIFICACION }}
                        </div>
                        <div class="flex flex-row">
                            <strong>FECHA:</strong> {{ $h.formatDate(modal_data.FECHA) }}
                        </div>
                        <div class="flex flex-row">
                            <strong>VENCIMIENTO:</strong> {{ $h.formatDate(modal_data.VENCIMIENTO) }}
                        </div>
                        <div class="flex flex-row">
                            <strong>VENDEDOR:</strong> {{ modal_data.NOMVENDEDOR }}
                        </div>
                    </div>

                    <table class="table table-sm mt-5 text-center">
                        <thead>
                        <tr>
                            <th class="border border-b-2 dark:border-dark-5">
                                COD
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                DESCRIPCIÓN
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                ARTE
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                MARCA
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                PRECIO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                CANTIDAD
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                TOTAL
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in modal_data.details">
                            <td class="border dark:border-dark-5">{{ item.CodigoProducto }}</td>
                            <td class="border dark:border-dark-5">{{ item.DescripcionProducto }}</td>
                            <td class="border dark:border-dark-5">{{ item.ARTE }}</td>
                            <td class="border dark:border-dark-5">{{ item.Marca }}</td>
                            <td class="border dark:border-dark-5 text-right">{{ $h.formatCurrency(item.Precio) }}</td>
                            <td class="border dark:border-dark-5 text-right">{{ item.Cantidad }}</td>
                            <td class="border dark:border-dark-5 text-right">
                                {{ $h.formatCurrency(item.ValorMercancia) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>


                    <table class="table table--sm mt-5 text-center">
                        <thead>
                        <tr>
                            <th class="border border-b-2 dark:border-dark-5">
                                BRUTO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                DESCUENTO
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                RTEFTE
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                RTEIVA
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                RTEICA
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                SUBTOTAL
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                IVA
                            </th>
                            <th class="border border-b-2 dark:border-dark-5">
                                TOTAL
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.BRUTO) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.DESCUENTO) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.RTEFTE) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.RTEIVA) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.RTEICA) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.SUBTOTAL) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(modal_data.IVA) }}
                            </td>
                            <td class="border dark:border-dark-5">
                                {{ $h.formatCurrency(parseFloat(modal_data.SUBTOTAL) + parseFloat(modal_data.IVA)) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </template>

                <template #footer>
                    <button @click="closeModal()" type="button" class="btn btn-secondary">
                        Cerrar
                    </button>
                </template>
            </jet-dialog-modal>
        </div>

    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import dayjs from "dayjs";

export default {
    props: {
        data: Array
    },

    components: {
        JetDialogModal,
        Head
    },

    data() {
        return {
            columns: [
                'NUMERO',
                'OC',
                'CLIENTE',
                'BRUTO',
                'DESCUENTO',
                'RTEFTE',
                'RTEIVA',
                'RTEICA',
                'SUBTOTAL',
                'IVA',
                'actions'
            ],
            options: {
                headings: {
                    NUMERO: '#',
                    OC: 'OC',
                    CLIENTE: 'CLIENTE',
                    BRUTO: 'BRUTO',
                    DESCUENTO: 'DESCUENTO',
                    RTEFTE: 'RTEFTE',
                    RTEIVA: 'RTEIVA',
                    RTEICA: 'RTEICA',
                    SUBTOTAL: 'SUBTOTAL',
                    IVA: 'IVA',
                    actions: '',
                },

                perPageValues: [10, 25, 50, 100, 250],
                clientSorting: false,
                sortable: ['NUMERO', 'OC', 'CLIENTE', 'BRUTO', 'DESC', 'SUBTOTAL'],
                templates: {
                    BRUTO: function (h, row) {
                        return <div class="text-right">

                            {this.$h.formatCurrency(row.BRUTO)}
                        </div>
                    },
                    DESCUENTO: function (h, row) {
                        return <div class="text-right">
                            {this.$h.formatCurrency(row.DESCUENTO)}
                        </div>
                    },
                    RTEFTE: function (h, row) {
                        return <div class="text-right">
                            {this.$h.formatCurrency(row.RTEFTE ?? 0)}
                        </div>
                    },
                    RTEIVA: function (h, row) {
                        return <div class="text-right">
                            {this.$h.formatCurrency(row.RTEIVA ?? 0)}
                        </div>
                    },
                    RTEICA: function (h, row) {
                        return <div class="text-right">
                            {this.$h.formatCurrency(row.RTEICA ?? 0)}
                        </div>
                    },
                    SUBTOTAL: function (h, row) {
                        return <div class="text-right">
                            {this.$h.formatCurrency(row.SUBTOTAL ?? 0)}
                        </div>
                    },
                    IVA: function (h, row) {
                        return <div class="text-right">
                            {this.$h.formatCurrency(row.IVA ?? 0)}
                        </div>
                    },
                }

            },

            form: {
                date_range: ''
            },
            table_data: this.data,
            modal_data: null,
            isOpen: false,
            count: 0
        }
    },

    methods: {
        get_data() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.get(route('queries.billing-per-day.invoices'), {
                params: {
                    date: this.form.date_range
                }
            }).then(resp => {
                this.$swal.close()

                this.table_data = resp.data
                this.count++;
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        },

        detail(row) {
            this.modal_data = row
            this.isOpen = true
        },

        closeModal() {
            this.isOpen = false
            this.modal_data = null
        }
    },

    computed: {
        current_date() {
            return dayjs()
        },

        bruto: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(c.BRUTO || 0)
            }, 0)
        },

        discount: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(c.DESCUENTO || 0)
            }, 0)
        },

        rtefte: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(isNaN(parseFloat(c.RTEFTE)) ? 0 : parseFloat(c.RTEFTE) || 0)
            }, 0)
        },

        rteiva: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(isNaN(parseFloat(c.RTEIVA)) ? 0 : parseFloat(c.RTEIVA) || 0)
            }, 0)
        },

        rteica: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(isNaN(parseFloat(c.RTEICA)) ? 0 : parseFloat(c.RTEICA) || 0)
            }, 0)
        },

        subtotal: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(isNaN(parseFloat(c.SUBTOTAL)) ? 0 : parseFloat(c.SUBTOTAL) || 0)
            }, 0)
        },

        iva: function () {
            return this.table_data.reduce(function (a, c) {
                return a + Number(c.IVA || 0)
            }, 0)
        },

        date_range: function () {
            return this.form.date_range
        }
    },

    watch: {
        date_range: function () {
            this.count === 0 ? this.count++ : this.get_data()
        }
    }
}
</script>

