<template>
    <div>
        <Head title="Produccion - Control entregas inyección" />

        <portal to="application-title">
            Produccion - Control entregas inyección
        </portal>

        <portal to="actions" >
            <a class="btn btn-primary" :href="route('report-production.control-entregas-inyeccion-pdf', form)" target="_blank">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte
            </a>

            <Link class="btn btn-secondary ml-2" :href="route('reports.index')">
                <font-awesome-icon icon="arrow-left" class="mr-2"/>
                Atrás
            </Link>
        </portal>

        <div>
            <div class="flex">
                <div class="grid grid-cols-2 gap-5 w-1/2 mr-auto">
                    <Litepicker
                        v-model="form.date_range"
                        :options="{
                    autoApply: true,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    format: 'DD-MM-YYYY',
                    lang: 'es-ES',
                    dropdowns: {
                        minYear: 2022,
                        maxYear: null,
                        months: true,
                        years: true
                    },
                    maxDate: current_date
                }"
                        class="form-control w-full"
                    />

                    <button class="btn btn-primary" @click="queryPending">Consultar</button>
                </div>

                <div class="items-center justify-items-center font-bold text-lg">
                    TOTALES: {{ total }}
                </div>
            </div>

            <v-client-table :data="table.data" :columns="table.columns" :options="table.options" class="overflow-y-auto"/>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3'
import dayjs from "dayjs";

export default {
    components: {
        Head,
        Link
    },

    data(){
        return {
            table: {
                data: [],
                columns: [
                    'OP',
                    'OV',
                    'REFERENCIA',
                    'COD_PROD',
                    'PRODUCTO',
                    'CANT',
                    'OPERACION',
                    'LOTE',
                    'FECHA',
                    'ACABADO_GALV',
                    'ARTE'
                ],
                options: {
                    headings: {
                        OP: 'OP',
                        REFERENCIA: 'REFERENCIA',
                        COD_PROD: 'CODIGO PRODUCTO',
                        PRODUCTO: 'PRODUCTO',
                        CANT: 'CANTIDAD',
                        OPERACION: 'OPERACION',
                        LOTE: 'LOTE',
                        FECHA: 'FECHA',
                        ACABADO_GALV: 'ACABADO GALVANICO',
                        ARTE: 'ARTE'
                    },
                    templates: {
                        CANT(h, row){
                            return parseInt(row.CANT)
                        }
                    },
                    cellClasses: {
                        CANT: [{
                            class: 'text-right',
                            condition: row => row
                        }],
                    }
                }
            },
            form: {
                date_range: ''
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

            let fileName = "control-entregas-inyeccion.xlsx"
            axios.post(route('download-report-production.control-entregas-inyeccion'), {
                date_range: this.form.date_range
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

        queryPending(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Consultando información…',
                text: 'Este proceso puede tomar unos segundos, espere por favor…',
            });

            axios.get(route('report-production.get-control-entrega-inyeccion'), {
                params: {
                    date_range: this.form.date_range
                }
            }).then(resp => {
                this.table.data = resp.data
                this.$swal.close()
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
    },

    computed: {
        current_date() {
            return dayjs()
        },
        total(){
            return this.table.data.reduce(function (a, c) {
                return a + Number((c.CANT) || 0)
            }, 0);
        }
    }
}
</script>
