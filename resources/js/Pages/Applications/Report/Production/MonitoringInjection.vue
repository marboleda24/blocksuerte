<template>
    <div>
        <Head title="Reportes - Produccion - Seguimiento Inyeccion "/>

        <portal to="application-title">
            Reportes - Produccion - Seguimiento Inyeccion
        </portal>

        <portal to="actions">
            <div class="grid grid-cols-3 gap-3">
                    <Litepicker
                        v-model="date_range"
                        :options="{
                        autoApply: true,
                        singleMode: false,
                        numberOfColumns: 2,
                        numberOfMonths: 2,
                        showWeekNumbers: true,
                        format: 'DD-MM-YYYY',
                        lang: 'es-ES',
                        dropdowns: {
                            minYear: 2023,
                            maxYear: 2035,
                            months: true,
                            years: true
                        }
                    }"
                        class="form-control w-72"
                    />

            <button class="btn btn-primary" @click="downloadReport(date_range)">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte
            </button>

            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>

            </div>
        </portal>

        <div>
            <v-client-table :data="rows" :columns="table.columns" :options="table.options" class="overflow-y-auto"/>
        </div>
    </div>
</template>
<script >
import {Head, Link} from "@inertiajs/vue3";

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
            date_range: [],
            table: {
                columns: [
                    'OP',
                    'PRODUCTO',
                    'CANT',
                    'MARCA',
                    'ACABADO_GALV',
                    'TRASABILIDAD',
                    'OPERARIO',
                    'MAQ',
                    'FECHA',
                    'SEGUIMIENTO'
                ],
                options: {
                    headings: {
                        OP: 'OP',
                        PRODUCTO: 'PRODUCTO',
                        CANT: 'CANTIDAD ',
                        ACABADO_GALV: 'ACABADO GALV ',
                        MARCA: 'ARTE',
                        TRASABILIDAD: 'TRASABILIDAD',
                        OPERARIO: 'OPERARIO',
                        MAQ: 'MAQ',
                        FECHA: 'FECHA',
                        SEGUIMIENTO: 'SEGUIMIENTO',

                    },
                }
            }
        }
    },

    methods: {
        downloadReport(date_range) {
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Descargando informe…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            let fileName = "SeguimientoInyeccion .xlsx"
            axios.post(route('report-production.download-report-monitoring-injection'), {date_range}, {
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
