<template>
    <div>
        <Head title="Reportes - Produccion - Estatica" />

        <portal to="application-title">
            Reportes - Produccion - Estatica
        </portal>

        <portal to="actions" >
            <button class="btn btn-primary" @click="downloadPDF('days')">
                <font-awesome-icon icon="file-pdf" class="mr-2"/>
                Descargar PDF Ordenado por dias
            </button>

            <button class="btn btn-primary ml-2" @click="downloadPDF('PRODUCTO')">
                <font-awesome-icon icon="file-pdf" class="mr-2"/>
                Descargar PDF Ordenado por descripción
            </button>

            <button class="btn btn-primary ml-2" @click="downloadReport()">
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
                    'REFERENCIA',
                    'COD_PROD',
                    'PRODUCTO',
                    'ACABADO',
                    'MARCA',
                    'ARTE',
                    'CANTIDADES',
                    'FECHAS',
                    'days',
                    'PESO',
                    'DIAS_OV'
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
                        CANTIDADES: 'CANTIDADES',
                        FECHAS: 'FECHAS',
                        days: 'DIAS',
                        PESO: 'PESO',
                        DIAS_OV:'DIAS_OV'
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
                                <li>Estatica: {row.FECHA_MOV}</li>
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

            let fileName = "pendientes-estatica.xlsx"
            axios.post(route('report-production.download-report'), {
                plant: 'STAT'
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
        },

        downloadPDF(sortBy){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando informe…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('report-production.estatica-pdf', sortBy), {
                responseType: 'blob'
            }).then(resp => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });

                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', 'informe.pdf');
                document.body.appendChild(link);
                link.click();
                this.$swal.close();
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: err.response.data.message,
                    confirmButtonText: 'Aceptar'
                });
                console.log(err.data);
            })
        }
    }
}
</script>
