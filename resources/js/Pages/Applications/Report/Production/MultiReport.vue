<template>
    <div>
        <Head :title="`Produccion - ${report.title}`" />

        <portal to="application-title">
            Produccion - {{ report.title }}
        </portal>

        <portal to="actions" >
            <a class="btn btn-primary" :href="route('report-production.multi-report', [parseInt(report.code), 'report'])" target="_blank">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte
            </a>

            <a class="btn btn-warning ml-2"
               :href="route('report-production.multi-report', [parseInt(report.code), 'report', 'yes'])"
               target="_blank" v-if="report.code === '1'">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte ventas
            </a>

            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <v-client-table :data="data" :columns="table.columns" :options="table.options" class="overflow-y-auto"/>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'

export default {
    props: {
        data: Array,
        report: Object
    },

    components: {
        Head, Link
    },

    data() {
        return {
            table: {
                columns: [
                    'OP',
                    'OV',
                    'REFERENCIA',
                    'COD_PROD',
                    'PRODUCTO',
                    'ACABADO',
                    'LOTE',
                    'ARTE_OV',
                    'MARCA',
                    'CANT_COMPLETADA',
                    'CANT_PENDIENTE',
                    'FECHA_LIBERACION',
                    'FECHA_MOV',
                    'FECHA_INI',
                ],
                options: {
                    headings: {
                        OP: 'OP',
                        OV: 'OV',
                        REFERENCIA: 'DESCRIPCIÓN PRODUCTO',
                        COD_PROD: 'CODIGO PRODUCTO',
                        ACABADO: 'ACABADO',
                        LOTE: 'LOTE',
                        ARTE_OV: 'ARTE',
                        MARCA: 'MARCA',
                        SECUENCIA: 'SECUENCIA',
                        OPERACION: 'OPERACION',
                        CANT_COMPLETADA: 'CANT COMPLETADA',
                        CANT_PENDIENTE: 'CANT PENDIENTE',
                        FECHA_LIBERACION: 'FECHA LIBERACION',
                        FECHA_MOV: 'DIAS PLANTA',
                        FECHA_INI: 'DIAS OV'
                    },
                    templates: {
                        CANT_COMPLETADA(h, row){
                            return parseInt(row.CANT_COMPLETADA)
                        },
                        CANT_PENDIENTE(h, row){
                            return parseInt(row.CANT_PENDIENTE)
                        },
                        FECHA_MOV(h, row){
                            return parseInt(row.FECHA_MOV ?? 0)
                        },
                        FECHA_INI(h, row){
                            return parseInt(row.FECHA_INI ?? 0)
                        }
                    },
                    cellClasses: {
                        CANT_COMPLETADA: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        CANT_PENDIENTE: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        FECHA_MOV: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        FECHA_INI: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                    }
                }
            }
        }
    }

}

</script>

