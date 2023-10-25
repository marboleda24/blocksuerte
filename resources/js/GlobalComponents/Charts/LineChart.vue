<template>
    <Line :chart-data="chartData" :chart-options="chartOptions" />
</template>

<script lang="jsx">
import 'chartjs-adapter-moment'
import { Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    TimeScale,
    PointElement,
    CategoryScale
} from 'chart.js'

ChartJS.register(Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    TimeScale,
    PointElement,
    CategoryScale
)

export default {
    name: 'LineChart',
    components: { Line },
    props: {
        width: {
            type: Number,
            default: 200
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
