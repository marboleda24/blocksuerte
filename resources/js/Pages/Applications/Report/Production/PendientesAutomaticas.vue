<template>
    <div>
        <Head title="Reportes - Produccion - Pendientes automaticas"/>

        <portal to="application-title">
            Reportes - Produccion - Pendientes automaticas
        </portal>

        <portal to="actions">
            <button class="btn btn-primary" @click="downloadReport()">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte
            </button>

            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <v-client-table :data="rows" :columns="table.columns" :options="table.options" class="overflow-y-auto"/>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'

export default {
    props: {
        rows: Array
    },

    components: {
        Head,
        Link
    },

    data() {
        return {
            table: {
                columns: [
                    'OP',
                    'REFERENCIA',
                    'LOTE',
                    'COD_PROD',
                    'PRODUCTO',
                    'CANT_PEND',
                    'FECHA_LIB',
                    'ARTE',
                    'Marca',
                    'CANT_OP',
                    'FECHA_OV',
                    'DIAS_CT',
                ],
                options: {
                    headings: {
                        OP: 'OP',
                        REFERENCIA: 'REFERENCIA',
                        COD_PROD: 'CODIGO',
                        PRODUCTO: 'DESCRIPCION',
                        ARTE: 'ARTE',
                        Marca: 'MARCA',
                        LOTE: 'LOTE',
                        CANT_PEND: 'CANTIDAD PENDIENTE',
                        CANT_OP: 'CANTIDAD OP',
                        FECHA_OV: 'FECHA OV',
                        FECHA_LIB: 'FECHA LIBERACION',
                        DIAS_CT: 'DIAS OPERACION',
                    },
                    templates: {
                        FECHA_LIB(h, row) {
                            return this.$h.formatDate(row.FECHA_LIB, 'YYYY-MM-DD')
                        }
                    },
                    cellClasses: {
                        CANT_PEND: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                        DIAS_OPERACION: [{
                            class: 'text-right',
                            condition: row => row
                        }],

                        DIAS_OV: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                    }
                },
            }
        }
    },

    methods: {
        downloadReport() {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando informe…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            let fileName = "pendientes-automatica.xlsx"
            axios.post(route('report-production.download-report.pendientes-automaticas'), {}, {
                responseType: 'blob'
            }).then(resp => {
                this.$swal.close()
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', fileName)
                document.body.appendChild(link)
                link.click()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        }
    }
}
</script>

