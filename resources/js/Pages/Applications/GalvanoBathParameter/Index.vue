<template>
    <div>
        <Head title="Parámetros Baños Galvano"/>

        <portal to="application-title">
            Parámetros Baños Galvano
        </portal>

        <v-client-table :data="records" :columns="table.columns" :options="table.options">

        </v-client-table>

    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import dayjs from "dayjs";

export default {
    props: {
        records: Array
    },

    components: {
        Head
    },

    data(){
        return {
            table: {
                columns: [
                    'production_order',
                    'product',
                    'bath',
                    'ph',
                    'density',
                    'temperature',
                    'entry_time',
                    'exit_time',
                    'user',
                    'notes',
                    'created_at'
                ],
                options: {
                    headings: {
                        production_order: 'OP',
                        product: 'PRODUCTO',
                        bath: 'BAÑO',
                        ph: 'PH',
                        density: 'DENSIDAD',
                        temperature: 'TEMPERATURA',
                        entry_time: 'HORA ENTRADA',
                        exit_time: 'HORA SALIDA',
                        user: 'REGISTRO',
                        notes: 'NOTAS',
                        created_at: 'FECHA',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['production_order', 'product', 'bath', 'ph', 'density', 'temperature', 'entry_time', 'exit_time', 'user', 'created_at'],
                    templates: {
                        product: function (h, row) {
                            return row.product.Pieza + ' - ' + row.product.Descripcion
                        },
                        entry_time: function (h, row) {
                            return dayjs('1/1/1 ' + row.entry_time).format('hh:mm a')
                        },
                        exit_time: function (h, row) {
                            return dayjs('1/1/1 ' + row.exit_time).format('hh:mm a')
                        },
                        user: function (h, row) {
                            return row.user.name
                        },
                        created_at: function (h, row){
                            return dayjs(new Date(row.created_at)).format('DD-MM-YYYY');
                        }
                    },
                    cellClasses: {
                        quantity: [{class: 'text-right', condition: row => row}],
                        sync: [{class: 'text-center', condition: row => row}],
                        action: [{class: 'text-center', condition: row => row}],
                    }
                }
            }
        }
    }
}
</script>
