<template>
    <div>
        <Head title="SST – Reportes"/>

        <portal to="application-title">
            SST – Reportes
        </portal>

        <div>
            <div class="box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">
                        TOTAL HORAS CONCEPTO - MES
                    </h2>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center">
                                <th colspan="15" class="whitespace-nowrap">HORA H W</th>
                            </tr>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">AÑO</th>
                                <th class="whitespace-nowrap">ENE</th>
                                <th class="whitespace-nowrap">FEB</th>
                                <th class="whitespace-nowrap">MAR</th>
                                <th class="whitespace-nowrap">ABR</th>
                                <th class="whitespace-nowrap">MAY</th>
                                <th class="whitespace-nowrap">JUN</th>
                                <th class="whitespace-nowrap">JUL</th>
                                <th class="whitespace-nowrap">AGO</th>
                                <th class="whitespace-nowrap">SEP</th>
                                <th class="whitespace-nowrap">OCT</th>
                                <th class="whitespace-nowrap">NOV</th>
                                <th class="whitespace-nowrap">DIC</th>
                                <th class="whitespace-nowrap">TOTAL AÑO</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, key) in hours_concept_month.hour_hw">
                                <th>{{ key }}</th>
                                <td :class="getHours(row, '1', true)">{{ getHours(row, '1') }}</td>
                                <td :class="getHours(row, '2', true)">{{ getHours(row, '2') }}</td>
                                <td :class="getHours(row, '3', true)">{{ getHours(row, '3') }}</td>
                                <td :class="getHours(row, '4', true)">{{ getHours(row, '4') }}</td>
                                <td :class="getHours(row, '5', true)">{{ getHours(row, '5') }}</td>
                                <td :class="getHours(row, '6', true)">{{ getHours(row, '6') }}</td>
                                <td :class="getHours(row, '7', true)">{{ getHours(row, '7') }}</td>
                                <td :class="getHours(row, '8', true)">{{ getHours(row, '8') }}</td>
                                <td :class="getHours(row, '9', true)">{{ getHours(row, '9') }}</td>
                                <td :class="getHours(row, '10', true)">{{ getHours(row, '10') }}</td>
                                <td :class="getHours(row, '11', true)">{{ getHours(row, '11') }}</td>
                                <td :class="getHours(row, '12', true)">{{ getHours(row, '12') }}</td>
                                <td class="text-right">{{
                                        parseInt(row.reduce(function (a, c) {
                                            return a + Number((c.hours) || 0)
                                        }, 0))
                                    }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="overflow-x-auto mt-5">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center">
                                <th colspan="15" class="whitespace-nowrap">INCAPACIDAD</th>
                            </tr>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">AÑO</th>
                                <th class="whitespace-nowrap">ENE</th>
                                <th class="whitespace-nowrap">FEB</th>
                                <th class="whitespace-nowrap">MAR</th>
                                <th class="whitespace-nowrap">ABR</th>
                                <th class="whitespace-nowrap">MAY</th>
                                <th class="whitespace-nowrap">JUN</th>
                                <th class="whitespace-nowrap">JUL</th>
                                <th class="whitespace-nowrap">AGO</th>
                                <th class="whitespace-nowrap">SEP</th>
                                <th class="whitespace-nowrap">OCT</th>
                                <th class="whitespace-nowrap">NOV</th>
                                <th class="whitespace-nowrap">DIC</th>
                                <th class="whitespace-nowrap">TOTAL AÑO</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, key) in hours_concept_month.inability">
                                <th>{{ key }}</th>
                                <td :class="getHours(row, '1', true)">{{ getHours(row, '1') }}</td>
                                <td :class="getHours(row, '2', true)">{{ getHours(row, '2') }}</td>
                                <td :class="getHours(row, '3', true)">{{ getHours(row, '3') }}</td>
                                <td :class="getHours(row, '4', true)">{{ getHours(row, '4') }}</td>
                                <td :class="getHours(row, '5', true)">{{ getHours(row, '5') }}</td>
                                <td :class="getHours(row, '6', true)">{{ getHours(row, '6') }}</td>
                                <td :class="getHours(row, '7', true)">{{ getHours(row, '7') }}</td>
                                <td :class="getHours(row, '8', true)">{{ getHours(row, '8') }}</td>
                                <td :class="getHours(row, '9', true)">{{ getHours(row, '9') }}</td>
                                <td :class="getHours(row, '10', true)">{{ getHours(row, '10') }}</td>
                                <td :class="getHours(row, '11', true)">{{ getHours(row, '11') }}</td>
                                <td :class="getHours(row, '12', true)">{{ getHours(row, '12') }}</td>
                                <td class="text-right">{{
                                        parseInt(row.reduce(function (a, c) {
                                            return a + Number((c.hours) || 0)
                                        }, 0))
                                    }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="overflow-x-auto mt-5">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center">
                                <th colspan="15" class="whitespace-nowrap">ACC W</th>
                            </tr>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">AÑO</th>
                                <th class="whitespace-nowrap">ENE</th>
                                <th class="whitespace-nowrap">FEB</th>
                                <th class="whitespace-nowrap">MAR</th>
                                <th class="whitespace-nowrap">ABR</th>
                                <th class="whitespace-nowrap">MAY</th>
                                <th class="whitespace-nowrap">JUN</th>
                                <th class="whitespace-nowrap">JUL</th>
                                <th class="whitespace-nowrap">AGO</th>
                                <th class="whitespace-nowrap">SEP</th>
                                <th class="whitespace-nowrap">OCT</th>
                                <th class="whitespace-nowrap">NOV</th>
                                <th class="whitespace-nowrap">DIC</th>
                                <th class="whitespace-nowrap">TOTAL AÑO</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, key) in hours_concept_month.accw">
                                <th>{{key}}</th>
                                <td :class="getHours(row, '1', true)">{{ getHours(row, '1') }}</td>
                                <td :class="getHours(row, '2', true)">{{ getHours(row, '2') }}</td>
                                <td :class="getHours(row, '3', true)">{{ getHours(row, '3') }}</td>
                                <td :class="getHours(row, '4', true)">{{ getHours(row, '4') }}</td>
                                <td :class="getHours(row, '5', true)">{{ getHours(row, '5') }}</td>
                                <td :class="getHours(row, '6', true)">{{ getHours(row, '6') }}</td>
                                <td :class="getHours(row, '7', true)">{{ getHours(row, '7') }}</td>
                                <td :class="getHours(row, '8', true)">{{ getHours(row, '8') }}</td>
                                <td :class="getHours(row, '9', true)">{{ getHours(row, '9') }}</td>
                                <td :class="getHours(row, '10', true)">{{ getHours(row, '10') }}</td>
                                <td :class="getHours(row, '11', true)">{{ getHours(row, '11') }}</td>
                                <td :class="getHours(row, '12', true)">{{ getHours(row, '12') }}</td>
                                <td class="text-right">{{
                                        parseInt(row.reduce(function (a, c) {
                                            return a + Number((c.hours) || 0)
                                        }, 0))
                                    }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto mt-5">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr class="text-center">
                                <th colspan="15" class="whitespace-nowrap">Empleados Año - Mes</th>
                            </tr>
                            <tr class="text-center">
                                <th class="whitespace-nowrap">AÑO</th>
                                <th class="whitespace-nowrap">ENE</th>
                                <th class="whitespace-nowrap">FEB</th>
                                <th class="whitespace-nowrap">MAR</th>
                                <th class="whitespace-nowrap">ABR</th>
                                <th class="whitespace-nowrap">MAY</th>
                                <th class="whitespace-nowrap">JUN</th>
                                <th class="whitespace-nowrap">JUL</th>
                                <th class="whitespace-nowrap">AGO</th>
                                <th class="whitespace-nowrap">SEP</th>
                                <th class="whitespace-nowrap">OCT</th>
                                <th class="whitespace-nowrap">NOV</th>
                                <th class="whitespace-nowrap">DIC</th>
                                <th class="whitespace-nowrap">TOTAL AÑO</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, key) in hours_concept_month.count">
                                    <th>{{ key }}</th>
                                    <td class="text-right" v-for="month in row">{{ month.quantity }}</td>
                                    <td class="text-center" v-if="row.length < 12" v-for="month in 12 - row.length ">
                                        –
                                    </td>
                                    <td class="text-right">{{
                                            parseInt(row.reduce(function (a, c) {
                                                return a + Number((c.quantity) || 0)
                                            }, 0))
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5 mt-5">
                <div class="box col-span-2">
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-4">
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
                                        minYear: 2017,
                                        maxYear: null,
                                        months: true,
                                        years: true
                                    },
                                    maxDate: current_date
                                }"
                                class="form-control w-full"
                            />

                            <button class="btn btn-primary" @click="downloadAbsenteeism()">Descargar ausentismo</button>
                        </div>
                    </div>
                </div>
                <div class="box col-span-2">
                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-4">
                            <TomSelect v-model="years"
                                       class="w-full"
                                       multiple>
                                <option v-for="year in lastYears" :value="year">{{ year }}</option>
                            </TomSelect>

                            <button class="btn btn-primary" @click="updateCharts()">Actualizar</button>
                            <button class="btn btn-warning" @click="getReport()">Descargar</button>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">Causas</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart
                            :width="400"
                            :height="400"
                            :chart-data="inabilityChart.data"
                            :chart-options="inabilityChart.options"/>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">Dias perdidos por centro de trabajo</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart
                            :width="1200"
                            :height="400"
                            :chart-data="workCenterChart.data"
                            :chart-options="workCenterChart.options"/>
                    </div>
                </div>

                <div class="box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                        <h2 class="font-medium text-base mr-auto">Cantidad de diagnósticos</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart
                            :width="1200"
                            :height="400"
                            :chart-data="diagnosticChart.data"
                            :chart-options="diagnosticChart.options"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head, Link} from "@inertiajs/vue3";
import BarChart from "@/GlobalComponents/Charts/BarChart.vue";
import DoughnutChart from "@/GlobalComponents/Charts/DoughnutChart.vue";
import dayjs from "dayjs";

let currentDate = dayjs();
let year = currentDate.year();

export default {
    components: {
        Head,
        Link,
        BarChart,
        DoughnutChart
    },

    props: {
        hours_concept_month: Object,
        colors: {
            type: Array,
            default: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
            ],
        }
    },

    data() {
        return {
            date_range: '',
            years: [year],
            inabilityChart: {
                type: 'bar',
                options: {
                    legend: {
                        display: false,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 75,
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            grid: {
                                display: false,
                            },
                        }
                    }
                },
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Tipo de incapacidad',
                        data: [],
                        backgroundColor: this.colors,
                        borderColor: this.colors,
                    }]
                }
            },
            workCenterChart: {
                type: 'bar',
                options: {
                    legend: {
                        display: false,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 75,
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            grid: {
                                display: false,
                            },
                        }
                    }
                },
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Centros de trabajo',
                        data: [],
                        backgroundColor: this.colors,
                        borderColor: this.colors,
                    }]
                }
            },
            diagnosticChart: {
                type: 'bar',
                options: {
                    legend: {
                        display: false,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 75,
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            grid: {
                                display: false,
                            },
                        }
                    }
                },
                data: {
                    labels: [],
                    datasets: [{
                        label: 'SVE de interés',
                        data: [],
                        backgroundColor: this.colors,
                        borderColor: this.colors,
                    }]
                }
            },
        }
    },

    methods: {
        getHours(values, month, className = false) {
            const monthData = values.find(m => m.mes == month);
            if (className) {
                return monthData ? 'text-right' : 'text-center'
            }
            return monthData ? parseInt(monthData.hours) : '–';
        },

        getInabilityReport(years){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('work-health-security.report.inability'), {
                years
            }).then(resp => {
                this.inabilityChart.data.labels = resp.data.labels
                this.inabilityChart.data.datasets[0].data = resp.data.values
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        getWorkCenterReport(years){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('work-health-security.report.work-center'), {
                years
            }).then(resp => {
                this.workCenterChart.data.labels = resp.data.labels
                this.workCenterChart.data.datasets[0].data = resp.data.values
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        getDiagnosticReport(years){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('work-health-security.report.diagnostic'), {
                years
            }).then(resp => {
                this.diagnosticChart.data.labels = resp.data.labels
                this.diagnosticChart.data.datasets[0].data = resp.data.values
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        getReport(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('work-health-security.report.download'), {
                years: this.years,
                inability_chart: JSON.stringify(this.inabilityChart).replace(/"/g, "'"),
                work_center_chart: JSON.stringify(this.workCenterChart).replace(/"/g, "'"),
                diagnostic_chart: JSON.stringify(this.diagnosticChart).replace(/"/g, "'"),
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = window.URL.createObjectURL(new Blob([resp.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', 'reporte.pdf');
                document.body.appendChild(link);
                link.click();

                this.$swal.close()
            }).catch(err => {
                console.log(err)
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        },

        updateCharts(){
            this.getInabilityReport(this.years)
            this.getWorkCenterReport(this.years)
            this.getDiagnosticReport(this.years)
        },

        downloadAbsenteeism(){
            this.$swal({
                iconHtml: this.$h.loadIcon(),
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                title: 'Cargando información…',
                text: 'Este proceso puede tardar unos segundos.',
            });

            axios.post(route('work-health-security.report.export-absenteeism'), {
                date_range: this.date_range
            }, {
                responseType: 'blob'
            }).then(resp => {
                const url = URL.createObjectURL(new Blob([resp.data], {
                    type: 'application/vnd.ms-excel'
                }))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', 'report.xlsx')
                document.body.appendChild(link)
                link.click()
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Hubo un error procesando la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            })
        }
    },

    mounted() {
        this.updateCharts()
    },

    computed: {
        lastYears() {
            let currentDate = dayjs();
            let currentYear = currentDate.year();
            let lastYears = [];

            for (let i = 0; i < 6; i++) {
                lastYears.push(currentYear - i);
            }
            return lastYears;
        },

        current_date() {
            return dayjs()
        },
    }
}
</script>
