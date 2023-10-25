<template>
    <div>
        <Head title="Compromisos de entrega OV"/>

        <portal to="application-title">
            Compromisos de entrega OV
        </portal>

        <v-client-table :data="query"
                        :columns="table.columns"
                        :options="table.options"
                        class="overflow-y-auto">
        </v-client-table>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'

export default {
    props: {
        query: Array
    },

    components: {
        Head
    },

    data() {
        return {
            table: {
                columns: [
                    'order',
                    'quantity',
                    'dispatched',
                    'pending',
                    'oc',
                    'brand',
                    'date',
                    'customer',
                    'type',
                    'product',
                    'price',
                    'commitment',
                    'days_passed',
                    'days_left'
                ],
                options: {
                    headings: {
                        order: "PEDIDO",
                        quantity: "CANT. PEDIDA",
                        dispatched: "CANT. DESPACHADA",
                        pending: "CANT. PENDIENTE",
                        oc: "OC",
                        brand: "MARCA",
                        date: "FECHA",
                        customer: "CLIENTE",
                        type: "TIPO",
                        product: "PRODUCTO",
                        price: "PRECIO",
                        commitment: "COMPROMISO",
                        days_passed: "DIAS TRANSCURRIDOS",
                        days_left: "DIAS FALTANTES",
                    },
                    clientSorting: false,
                    sortable: ['order', 'quantity', 'dispatched', 'pending', 'oc', 'brand', 'date', 'customer', 'type', 'product', 'price', 'commitment', 'days_passed', 'days_left'],
                    templates: {
                        order(h, row) {
                            return `${row.OV}-${row.LINEA}${row.ITEM}`
                        },
                        quantity(h, row) {
                            return parseInt(row.CANTIDAD_ACTUAL)
                        },
                        dispatched(h, row) {
                            return parseInt(row.CANTIDAD_DESPACHADA)
                        },
                        pending(h, row) {
                            return parseInt(row.CANTIDAD_PENDIENTE)
                        },
                        oc(h, row) {
                            return row.OC
                        },
                        brand(h, row) {
                            return row.NOTAS
                        },
                        date(h, row) {
                            return this.$h.formatDate(row.FECHA_OV, 'YYYY-MM-DD')
                        },
                        customer(h, row) {
                            return row.RAZON_SOCIAL
                        },
                        type(h, row) {
                            return row.CLASIFICACION_CLIENTE
                        },
                        product(h, row) {
                            return row.PRODUCTO
                        },
                        price(h, row) {
                            return this.$h.formatCurrency(row.PRECIO)
                        },
                        commitment(h, row) {
                            return this.$h.formatDate(row.FECHA_ENTREGA, 'YYYY-MM-DD')
                        },
                        days_passed(h, row) {
                            return row.DIAS_TRANSCURRIDOS
                        },
                        days_left(h, row) {
                            return row.DIAS_FALTANTES
                        }

                    },
                    cellClasses: {
                        quantity: [{class: 'text-right', condition: row => row}],
                        dispatched: [{class: 'text-right', condition: row => row}],
                        pending: [{class: 'text-right', condition: row => row}],
                        price: [{class: 'text-right', condition: row => row}],
                        days_passed: [{class: 'text-center', condition: row => row}],
                        days_left: [{class: 'text-center', condition: row => row}],
                    },
                    rowClassCallback(row) {
                        if (row.DIAS_FALTANTES < 5) {
                            return 'bg-red-200 dark:bg-red-800'
                        } else if (row.DIAS_FALTANTES > 5 && row.DIAS_FALTANTES < 10) {
                            return 'bg-yellow-200 dark:bg-yellow-800'
                        } else if (row.DIAS_FALTANTES >= 10 && row.DIAS_FALTANTES <= 15) {
                            return 'bg-green-200 dark:bg-green-800'
                        }
                    }

                }
            }
        }
    }
}
</script>
