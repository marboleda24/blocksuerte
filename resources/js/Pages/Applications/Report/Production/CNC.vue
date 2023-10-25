<template>
    <div>
        <Head title="Reportes - Produccion - CNC" />

        <portal to="application-title">
            Reportes > Produccion - CNC
        </portal>

        <portal to="actions" >
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

    data(){
        return {
            table: {
                columns: [
                    'OP',
                    'OV',
                    'REFERENCIA',
                    'COD_PROD',
                    'PRODUCTO',
                    'ACABADO',
                    'MARCA',
                    'ARTE',
                    'OPERACION',
                    'CANTIDADES',
                    'FECHAS',
                    'days',
                ],
                options: {
                    headings: {
                        OP: 'OP',
                        REFERENCIA: 'REFERENCIA',
                        COD_PROD: 'CODIGO PRODUCTO',
                        PRODUCTO: 'PRODUCTO',
                        ACABADO: 'ACABADO',
                        MARCA: 'MARCA',
                        ARTE: 'ARTE',
                        OPERACION: 'OPERACION',
                        CANTIDADES: 'CANTIDADES',
                        FECHAS: 'FECHAS',
                        days: 'DIAS',
                    },
                    templates: {
                        CANTIDADES(h, row){
                            return <ul>
                                <li>Completada: {row.CANT_COMPLETADA}</li>
                                <li>Pendiente: {row.CANT_PENDIENTE}</li>
                            </ul>
                        },
                        FECHAS(h, row){
                            return <ul>
                                <li>Liberacion: {row.FECHA_LIBERACION}</li>
                                <li>CNC: {row.FECHA_MOV}</li>
                            </ul>
                        },
                    }
                },


            }
        }
    },

    methods: {
        downloadReport(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando informe…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            let fileName = "pendientes-cnc.xlsx"
            axios.post(route('report-production.download-report'), {
                plant: 'CNC'
            }, {
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
