<template>
    <div>
        <Head title="Gestion Ambiental"/>

        <portal to="application-title">
            Gestion Ambiental
        </portal>

        <div>
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Sensor de Chimenea</h2>

                    <Litepicker
                        v-model="chimney_daterange"
                        :options="{
                            autoApply: false,
                            singleMode: false,
                            numberOfColumns: 2,
                            numberOfMonths: 2,
                            showWeekNumbers: true,
                            format: 'DD-MM-YYYY',
                            lang: 'es-ES',
                            dropdowns: {
                                minYear: 2021,
                                maxYear: null,
                                months: true,
                                years: true
                            },
                        }"
                        class="form-control w-64 ml-auto"
                    />

                    <div class="form-check form-switch mt-3 sm:mt-0 ml-2">
                        <input v-model="disabled_notification" @click="change_state"
                               data-target="#head-options-table"
                               class="show-code form-check-input mr-0 ml-3"
                               type="checkbox">
                    </div>

                </div>
                <div class="p-5">
                    <line-chart
                        :width="width"
                        :height="height"
                        :chart-data="data_chimey"
                        :chart-options="options_chimey"

                        ref="chimney"/>
                </div>
            </div>

            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
                    <h2 class="font-medium text-base mr-auto uppercase">Sensor de Gas</h2>

                    <Litepicker
                        v-model="gas_daterange"
                        :options="{
                            autoApply: false,
                            singleMode: false,
                            numberOfColumns: 2,
                            numberOfMonths: 2,
                            showWeekNumbers: true,
                            format: 'DD-MM-YYYY',
                            lang: 'es-ES',
                            dropdowns: {
                                minYear: 2021,
                                maxYear: null,
                                months: true,
                                years: true
                            },
                        }"
                        class="form-control w-64"
                    />
                </div>
                <div class="p-5">
                    <line-chart
                        :width="width"
                        :height="height"
                        :chart-data="data_gas"
                        :chart-options="options_gas"
                        ref="gas"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="jsx">
import dayjs from "dayjs";
import {Head, Link} from '@inertiajs/vue3'
import LineChart from "@/GlobalComponents/Charts/LineChart.vue";

import 'dayjs/locale/es'
dayjs.locale('es');

export default {
    components: {
        Head,
        Link,
        LineChart
    },

    props: {
        width: {
            type: Number,
            default: 0
        },
        height: {
            type: Number,
            default: 200
        },
        chimey1: Array,
        chimey2: Array,
        chimey_labels: Array,
        gas: Array,
        gas_labels: Array,
        notify: Boolean
    },

    data() {
        return {
            data_chimey: {
                labels: this.chimey_labels,
                datasets: [
                    {
                        label: "Chimenea 1",
                        data: this.chimey1,
                        borderWidth: 2,
                        borderColor: "#3160D8",
                        backgroundColor: "#3160D8",
                        pointBorderColor: "#3160D8",
                        fill: false
                    },
                    {
                        label: "Chimenea 2",
                        data: this.chimey2,
                        borderWidth: 2,
                        borderColor: "#CC0606",
                        backgroundColor: "#CC0606",
                        pointBorderColor: "#CC0606",
                        fill: false
                    }
                ]
            },
            data_gas: {
                labels: this.gas_labels,
                datasets: [
                    {
                        label: "Gas",
                        data: this.gas,
                        borderWidth: 2,
                        borderColor: "#3160D8",
                        backgroundColor: "#3160D8",
                        pointBorderColor: "#3160D8",
                        fill: false
                    }
                ]
            },
            options_chimey: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            stepSize: 1, // I'm using 3 hour intervals here
                            tooltipFormat: 'DD-MM-YYYY hh:mm A',
                        },
                        ticks: {
                            major: {
                                enabled: true, // <-- This is the key line
                                fontStyle: 'bold', //You can also style these values differently
                                fontSize: 12 //You can also style these values differently
                            },
                        },
                        gridLines: {
                            display: true
                        },
                    },
                    y: {
                        ticks: {
                            fontSize: "12",
                            fontColor: "#777777",
                            callback: function (value) {
                                return value + "°";
                            }
                        },
                        gridLines: {
                            color: "#D8D8D8",
                            zeroLineColor: "#D8D8D8",
                            borderDash: [2, 2],
                            zeroLineBorderDash: [2, 2],
                            drawBorder: true
                        }
                    }
                },
                maintainAspectRatio: false
            },
            options_gas: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            stepSize: 1, // I'm using 3 hour intervals here
                            tooltipFormat: 'DD-MM-YYYY hh:mm A',
                        },
                        ticks: {
                            major: {
                                enabled: true, // <-- This is the key line
                                fontStyle: 'bold', //You can also style these values differently
                                fontSize: 12 //You can also style these values differently
                            },
                        },
                        gridLines: {
                            display: true
                        },
                    },
                    y: {
                        ticks: {
                            fontSize: "12",
                            fontColor: "#777777",
                            callback: function (value) {
                                return value + "m^3";
                            }
                        },
                        gridLines: {
                            color: "#D8D8D8",
                            zeroLineColor: "#D8D8D8",
                            borderDash: [2, 2],
                            zeroLineBorderDash: [2, 2],
                            drawBorder: true
                        }
                    }
                },
                maintainAspectRatio: false
            },
            disabled_notification: this.notify,
            chimney_daterange: "",
            gas_daterange: "",
        }
    },

    methods: {
        change_state() {
            axios.post(route('environmental-management.disable-notify'), {
                state: this.disabled_notification
            }).then(resp => {
                this.$swal.close()
                this.disabled_notification = resp.data.state
            }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: 'Hubo un error actualizando la información',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                })
                console.log(error);
            })
        },
        get_new_data(startDate, endDate, type) {
            axios.get(route('chimney-gas.chimney-new-data'), {
                params: {
                    startDate: startDate,
                    endDate: endDate,
                    type: type
                }
            }).then(resp => {
                if (type === 'chimney') {
                    let chart = {
                        labels: resp.data.labels,
                        datasets: [
                            {
                                label: "Chimenea 1",
                                data: resp.data.chimey1,
                                borderWidth: 2,
                                borderColor: "#3160D8",
                                backgroundColor: "#3160D8",
                                pointBorderColor: "#3160D8",
                                fill: false
                            },
                            {
                                label: "Chimenea 2",
                                data: resp.data.chimey2,
                                borderWidth: 2,
                                borderColor: "#CC0606",
                                backgroundColor: "#CC0606",
                                pointBorderColor: "#CC0606",
                                fill: false
                            }
                        ]
                    }

                    //update data
                    this.data_chimey = chart;

                } else {
                    let chart = {
                        labels: resp.data.labels,
                        datasets: [
                            {
                                label: "Gas",
                                data: resp.data.gas,
                                borderWidth: 2,
                                borderColor: "#3160D8",
                                backgroundColor: "#3160D8",
                                pointBorderColor: "#3160D8",
                                fill: false
                            }
                        ]
                    }
                    this.data_gas = chart;
                }
                this.$swal.close()
            }).catch(err => {
                this.$swal({
                    icon: 'error',
                    title: 'Ups.. Hubo un error procesando la solicitud',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    timer: 6000,
                });
                console.log(err);
            })
        }
    },

    watch: {
        chimney_daterange(newValue) {
            let startDate;
            let endDate;
            [startDate, endDate] = newValue.split(' - ');
            this.get_new_data(startDate, endDate, 'chimney');

        },

        gas_daterange(newValue) {
            let startDate;
            let endDate;
            [startDate, endDate] = newValue.split(' - ');
            this.get_new_data(startDate, endDate, 'gas');
        },
    },
}
</script>
