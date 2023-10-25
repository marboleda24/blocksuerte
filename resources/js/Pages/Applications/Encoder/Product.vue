<template>
    <div>
        <portal to="breadcrumb">
            <h2 class="text-lg font-medium">
                Productos Viejos / Nuevos
            </h2>
        </portal>

        <v-client-table :data="products" :columns="table.columns" :options="table.options" />

    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import {defineComponent} from "vue";
import 'dayjs/locale/es'
dayjs.locale('es')

export default defineComponent({
    metaInfo: {
        title: 'Productos Viejos / Nuevos'
    },

    props: {
        products: Array
    },

    data(){
        return {
            table: {
                columns: [
                    'old_product',
                    'new_product',
                    'user',
                    'created_at',
                    'updated_at',
                ],
                options: {
                    headings: {
                        old_product: 'PRODUCTO VIEJO',
                        new_product: 'PRODUCTO NUEVO',
                        user: 'CREADO POR',
                        created_at: 'CREADO EL',
                        updated_at: 'ACTUALIZADO EL',
                    },
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: ['old_product', 'new_product', 'user', 'created_at', 'updated_at'],
                    templates: {
                        created_at: function (h, row) {
                            return dayjs(new Date(row.created_at)).format('DD MM YYYY hh:mm a');
                        },
                        updated_at: function (h, row) {
                            return dayjs(new Date(row.updated_at)).format('DD MM YYYY hh:mm a');
                        },
                        user: function (h, row) {
                            return row.user.name
                        }
                    }
                }
            }
        }
    }
})
</script>
