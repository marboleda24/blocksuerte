<template>
    <div>
        <Head title="Consulta Productos Cliente - OC"/>

        <portal to="application-title">
            Consulta Productos Cliente - OC
        </portal>

        <div>
            <div class="grid grid-cols-3 gap-5">
                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Orden de Compra
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>
                    <input type="text" class="form-control" v-model.trim="form.oc">
                </div>

                <div class="mt-2">
                    <label class="flex flex-col sm:flex-row">
                        Producto del Cliente
                        <span class="sm:ml-auto mt-1 sm:mt-0 text-xs text-gray-600">
                            Obligatorio
                        </span>
                    </label>
                    <input type="text" class="form-control" v-model.trim="form.cpc">
                </div>

                <div class="mt-2">
                    <button class="btn btn-primary w-full h-full" @click="search">
                        Consultar
                    </button>
                </div>
            </div>
        </div>

        <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto">
        </v-client-table>

    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import dayjs from "dayjs";

import 'dayjs/locale/es'
dayjs.locale('es')

export default {
    components: {
        Head
    },

    data: function () {
        return {
            table: {
                data: [],
                columns: [
                    'invoice',
                    'customer',
                    'oc',
                    'ov',
                    'cpc',
                    'art',
                    'brand',
                    'invoiced_quantity',
                    'date'
                ],
                options: {
                    headings: {
                        invoice: 'FACTURA',
                        customer: 'CLIENTE',
                        oc: 'OC',
                        ov: 'OV',
                        cpc: 'CÓDIGO PRODUCTO',
                        art: 'ARTE',
                        brand: 'MARCA',
                        invoiced_quantity: 'CANTIDAD FACTURADA',
                        date: 'FECHA'
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting : false,
                    sortable: ['invoice', 'customer', 'oc', 'ov', 'cpc', 'art', 'brand', 'invoiced_quantity', 'date'],
                    templates: {
                        invoice: function (h, row) {
                            return row.FACTURA
                        },
                        customer: function (h, row) {
                            return row.RAZON_SOCIAL
                        },
                        oc: function (h, row) {
                            return row.OC
                        },
                        ov: function (h, row) {
                            return row.OV
                        },
                        cpc: function (h, row) {
                            return row.CODPRODCLIENTE
                        },
                        art: function (h, row) {
                            return row.ARTE
                        },
                        brand: function (h, row) {
                            return row.MARCA
                        },
                        invoiced_quantity: function (h, row) {
                            return row.CANTIDAD_FACTURADA
                        },
                        date: function (h, row) {
                            return dayjs(new Date(row.FECHA)).format('DD MMMM YYYY')
                        },

                    },
                    cellClasses: {
                        invoiced_quantity: [{
                            class: 'text-right',
                            condition: row  => row
                        }],
                        art: [{
                            class: 'text-center',
                            condition: row  => row
                        }],
                        brand: [{
                            class: 'text-center',
                            condition: row  => row
                        }],
                        date: [{
                            class: 'text-center',
                            condition: row  => row
                        }],
                    }
                }
            },
            form: {
                oc: '',
                cpc: ''
            }
        }
    },

    methods: {
        search() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });
            axios.get(route('customer-product-oc.search'), {
                params: {
                    oc: this.form.oc,
                    cpc: this.form.cpc
                }
            }).then(resp => {
                this.$swal.close()
                this.table.data = resp.data
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar',
                });
                console.log(err);
            })
        }
    }
}
</script>
