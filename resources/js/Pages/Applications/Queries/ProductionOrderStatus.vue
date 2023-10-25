<template>
    <div>
        <Head title="Estado ordenes de producción"/>

        <portal to="application-title">
            Estado ordenes de producción
        </portal>

        <v-client-table :data="query"
                        :columns="table.columns"
                        :options="table.options"
                        class="overflow-y-auto">
        </v-client-table>
    </div>
</template>

<script lang="jsx">
import {Head} from "@inertiajs/vue3";
import diffTimeByNow from "@/utils/helper";

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
                    'op',
                    'plant',
                    'process',
                    'reference',
                    'product',
                    'ov',
                    'date_ov',
                    'oc',
                    'days_ov',
                    'pending',
                    'complete',
                    'customer',
                    'entry',
                    'days_ct'
                ],
                options: {
                    headings: {
                        op: 'OP',
                        plant: 'PLANTA',
                        process: 'PROCESO',
                        reference: 'REFERENCIA',
                        product: 'PRODUCTO',
                        ov: 'OV',
                        date_ov: 'FECHA OV',
                        oc: 'OC',
                        days_ov: 'DIAS OV',
                        pending: 'PENDIENTE',
                        complete: 'COMPLETADA',
                        customer: 'CLIENTE',
                        entry: 'INGRESO',
                        days_ct: 'DIAS CT'
                    },
                    clientSorting: false,
                    sortable: ['op', 'plant', 'process', 'reference', 'product', 'ov', 'date_ov', 'oc', 'days_ov', 'pending', 'complete', 'customer', 'entry', 'days_ct'],
                    templates: {
                        op(h, row) {
                            return row.ORDNUM_14
                        },
                        plant(h, row) {
                            return row.WRKCTR_14
                        },
                        process(h, row) {
                            return row.OPRDES_14
                        },
                        reference(h, row) {
                            return row.PRTNUM_14
                        },
                        product(h, row) {
                            return row.PMDES1_01
                        },
                        ov(h, row) {
                            return row.OV
                        },
                        date_ov(h, row) {
                            return row.FechaOV ? this.$h.formatDate(row.FechaOV, 'YYYY-MM-DD'): '-'
                        },
                        oc(h, row) {
                            return row.OC.trim() ? row.OC : '-'
                        },
                        days_ov(h, row){
                            return row.FechaOV ? Math.abs(this.$h.diffTimeByNow(row.FechaOV).days - 1) : '-'
                        },
                        pending(h, row) {
                            return parseInt(row.QTYREM_14)
                        },
                        complete(h, row) {
                            return parseInt(row.QTYCOM_14)
                        },
                        customer(h, row){
                            return row.Cliente
                        },
                        entry(h, row){
                            return row.MOVDTE_14 ? this.$h.formatDate(row.MOVDTE_14, 'YYYY-MM-DD'): '-'
                        },
                        days_ct(h, row){
                            return row.MOVDTE_14 ? Math.abs(this.$h.diffTimeByNow(row.MOVDTE_14).days - 1) : '-'
                        },
                    },

                    filterAlgorithm: {
                        op(row, query) {
                            return row.ORDNUM_14.toLowerCase().includes(query.toLowerCase())
                        },
                        plant(row, query) {
                            return row.WRKCTR_14.toLowerCase().includes(query.toLowerCase())
                        },
                        process(row, query) {
                            return row.OPRDES_14.toLowerCase().includes(query.toLowerCase())
                        },
                        reference(row, query) {
                            return row.PRTNUM_14.toLowerCase().includes(query.toLowerCase())
                        },
                        product(row, query) {
                            return row.PMDES1_01.toLowerCase().includes(query.toLowerCase())
                        },
                        ov(row, query) {
                            return row.OV.toLowerCase().includes(query.toLowerCase())
                        },
                        oc(row, query) {
                            return row.OC.toLowerCase().includes(query.toLowerCase())
                        },

                    },
                    cellClasses: {
                        plant: [{class: 'text-center', condition: row => row}],
                        ov: [{class: 'text-center', condition: row => row}],
                        date_ov: [{class: 'text-center', condition: row => row}],
                        oc: [{class: 'text-center', condition: row => row}],
                        days_ov: [{class: 'text-center', condition: row => row}],
                        pending: [{class: 'text-center', condition: row => row}],
                        complete: [{class: 'text-center', condition: row => row}],
                        entry: [{class: 'text-center', condition: row => row}],
                        days_ct: [{class: 'text-center', condition: row => row}],
                    }
                }
            }
        }
    }
}
</script>
