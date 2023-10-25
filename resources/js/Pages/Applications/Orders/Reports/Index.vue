<template>
    <div>
        <Head title="Reportes de pedidos"/>

        <portal to="application-title">
            Reportes de pedidos
        </portal>


        <div>
            <div class="grid grid-cols-2 gap-4">
                <div class="intro-y box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Pedidos por estado</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart :width="1200"
                                   :height="400"
                                   :chart-data="ordersStateChart.data"
                                   :chart-options="ordersStateChart.options"/>


                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div class="px-4 py-4 flex items-center zoom-in border-2 rounded-lg"
                                 v-for="(value, key) in orders_state.labels">
                                <div class="ml-2 mr-auto">
                                    <div class="font-medium">
                                        {{ value }}
                                    </div>
                                </div>
                                <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                    {{ orders_state.values[key] }} registros
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intro-y box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Pedidos finalizados por mes</h2>
                    </div>
                    <div class="p-5">
                        <bar-chart
                            :width="1200"
                            :height="600"
                            :chart-data="ordersDateChart.data"
                            :chart-options="ordersDateChart.options"/>
                    </div>
                </div>

                <div class="intro-y box col-span-2">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Ventas Registradas por mes</h2>
                    </div>
                    <div class="p-5">

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import {Head} from '@inertiajs/vue3'
import LineChart from "@/GlobalComponents/Charts/LineChart.vue";
import BarChart from "@/GlobalComponents/Charts/BarChart.vue";
import DoughnutChart from "@/GlobalComponents/Charts/DoughnutChart.vue";

export default {
    props: {
        orders_state: Object,
        orders_date: Object
    },

    components: {
        Head,
        LineChart,
        BarChart,
        DoughnutChart
    },

    data() {
        return {
            ordersStateChart: {
                options: {
                    legend: {
                        display: false,
                    },
                    responsive: true,
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
                    labels: this.orders_state.labels,
                    datasets: [{
                        label: 'Pedidos por estado',
                        data: this.orders_state.values,
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
            },
            ordersDateChart: {
                options: {
                    legend: {
                        display: false,
                    },
                    responsive: true,
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
                    labels: this.orders_date.labels,
                    datasets: [{
                        label: 'Pedidos finalizados por mes',
                        data: this.orders_date.values,
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
        }
    }
}
</script>
