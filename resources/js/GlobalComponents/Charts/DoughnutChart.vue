<template>
    <Doughnut :chart-data="chartData" :chart-options="chartOptions" />
</template>

<script lang="jsx">
import 'chartjs-adapter-moment'
import { Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
} from 'chart.js'

ChartJS.register(Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
)

export default {
    name: 'DoughnutChart',
    components: { Doughnut },
    props: {
        width: {
            type: Number,
            default: 100
        },
        height: {
            type: Number,
            default: 100
        },
        chartData: {
            type: Object,
            required: true
        },
        chartOptions: {
            type: Object,
            default: () => {}
        }
    },
}
</script>
