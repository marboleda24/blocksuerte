<template>
    <div>
        <Head title="Indicadores de calidad"/>

        <portal to="application-title">
            Indicadores de calidad
        </portal>

        <portal to="actions" >
            <button class="btn btn-primary" @click="downloadReport">
                <font-awesome-icon icon="download" class="mr-2"/>
                Descargar reporte
            </button>
        </portal>

        <div>
            <div class="grid grid-cols-4 gap-4">
                <div class="box">
                    <div class="flex flex-col lg:flex-row items-center p-5">
                        <div class="lg:mr-1 items-center">
                            <font-awesome-icon :icon="['far', 'clock']" size="3x"/>
                        </div>
                        <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                            <a href="" class="font-medium">
                                TIEMPO PARO DE MAQUINA
                            </a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                Tiempo que comienza desde la apertura de la solicitud de mantenimiento, hasta el cierre de todas las ordenes de trabajo y cierre de la solicitud
                            </div>
                        </div>
                        <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                            {{ time_close }} Horas Aprox.
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col lg:flex-row items-center p-5">
                        <div class="lg:mr-1 items-center">
                            <font-awesome-icon :icon="['fas', 'hourglass-end']" size="3x"/>
                        </div>
                        <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                            <a href="" class="font-medium">
                                MTF
                            </a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                Tiempo que se tarda el area de mantenimiento en dar solucion (tiempo de apertura de solicitud de mantenimiento - cierre de solicitud de mantenimiento)
                            </div>
                        </div>
                        <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                            {{ time_close }} Horas Aprox.
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col lg:flex-row items-center p-5">
                        <div class="lg:mr-1 items-center">
                            <font-awesome-icon :icon="['fas', 'money-check-dollar']" size="3x"/>
                        </div>
                        <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                            <a href="" class="font-medium">
                                COSTOS
                            </a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                tiempo en el cual la maquina esta parada * valor de costo por hora de produccion de esa maquina
                            </div>
                        </div>
                        <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                            $ 1.234.345,00 Aprox.
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col lg:flex-row items-center p-5">
                        <div class="lg:mr-1 items-center">
                            <font-awesome-icon :icon="['fas', 'business-time']" size="3x"/>
                        </div>
                        <div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
                            <a href="" class="font-medium">
                                TIEMPO TOTAL EN REPARACIONES
                            </a>
                            <div class="text-slate-500 text-xs mt-0.5">
                                Es el tiempo que se calcula desde el cierre de la ultima solicitud de mantenimiento , hasta la apertura de una nueva solicitud de mantenmiento.
                            </div>
                        </div>
                        <div class="flex mt-4 lg:mt-0 text-center text-slate-500 ml-1">
                            2 Horas
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Activos con mas mantenimientos</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart :width="1200"
                                   :height="400"
                                   :chart-data="amm_chart"
                                   :chart-options="options"/>
                    </div>
                </div>
                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Tipos de mantenimiento</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart :width="1200"
                                   :height="400"
                                   :chart-data="mt_chart"
                                   :chart-options="options"/>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from '@inertiajs/vue3';
import LineChart from "@/GlobalComponents/Charts/LineChart.vue";
import BarChart from "@/GlobalComponents/Charts/BarChart.vue";
import DoughnutChart from "@/GlobalComponents/Charts/DoughnutChart.vue";

export default {
    props: {
        assets_more_maintenance: Array,
        maintenance_types: Array,
        time_close: Number
    },
    components: {
        Head,
        Link,
        LineChart,
        BarChart,
        DoughnutChart
    },

    data(){
        return {
            options: {
                legend: {
                    display: false,
                },
                responsive: false,
                cutoutPercentage: 75,
            },
            amm_chart: {
                labels: this.assets_more_maintenance.labels,
                datasets: [{
                    label: 'Activos con mas mantenimientos',
                    data: this.assets_more_maintenance.values,
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                }]
            },

            mt_chart: {
                labels: this.maintenance_types.labels,
                datasets: [{
                    label: 'Tipos de mantenimiento',
                    data: this.maintenance_types.values,
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                }]
            }


        }
    },

    methods: {
        downloadReport() {
            let fileName = "maintenance-requests.xlsx"
            axios.get(route('maintenance.download-report'), {
                responseType: 'blob'
            }).then(resp => {
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

    }
}
</script>

<style scoped>

</style>
