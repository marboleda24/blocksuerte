<template>
    <div>
        <Head title="Productos codificados"/>

        <portal to="application-title">
            Productos codificados
        </portal>

        <div>
            <v-client-table :data="codes"
                            :columns="table.columns"
                            :options="table.options"
                            class="overflow-y-auto">
            </v-client-table>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'

export default {
    props: {
        codes: Array
    },

    components: {
        Head
    },

    data(){
        return {
            table: {
                columns: [
                    'code',
                    'description',
                    'product_type',
                    'line',
                    'subline',
                    'feature',
                    'material',
                    'measurement',
                    'galvanic_finish',
                    'decorative_option',
                    'max_status'
                ],
                options: {
                    headings: {
                        code: "CODIGO",
                        description: "DESCRIPCION",
                        product_type: "TIPO DE PRODUCTO",
                        line: "LINEA",
                        subline: "SUBLINEA",
                        feature: "CARACTERISTICA",
                        material: "MATERIAL",
                        measurement: "MEDIDA",
                        galvanic_finish: "ACABADO GALVANICO",
                        decorative_option: "OPCION DECORATIVA",
                        max_status: "ESTADO"
                    },

                    uniqueKey: "id",
                    perPageValues: [10, 25, 50, 100, 250],
                    clientSorting: false,
                    sortable: [
                        'code', 'description', 'product_type', 'line', 'subline', 'feature',
                        'material', 'measurement', 'galvanic_finish', 'decorative_option'
                    ],
                    templates: {
                        max_status (h, row) {
                            return row.max_status
                                ? <span className="badge badge-success badge-rounded">Sincronizado</span>
                                : <span class="badge badge-danger badge-rounded">Inexistente</span>
                        },

                    }
                },
            }
        }
    }
}
</script>

